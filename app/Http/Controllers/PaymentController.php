<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private function getCurrentTenantId()
    {
        return auth()->id();
    }

    private function authorizeTenantBooking(Booking $booking)
    {
        if ($booking->tenant_id !== $this->getCurrentTenantId()) {
            abort(403, 'Anda tidak memiliki hak akses ke transaksi ini.');
        }
    }

    public function index()
    {
        $payments = Payment::whereHas('booking', function ($query) {
            $query->where('tenant_id', $this->getCurrentTenantId());
        })->with('booking.room')->get();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $request->validate(['booking_id' => 'required|exists:bookings,id']);
        
        $booking = Booking::with('room')->findOrFail($request->booking_id);
        $this->authorizeTenantBooking($booking);

        if ($booking->status !== 'pending') {
            return redirect()->route('payments.index')
                             ->with('error', 'Booking ini sudah dibayar atau dibatalkan.');
        }

        $expectedAmount = $booking->room->price * $booking->duration_months;

        return view('payments.create', compact('booking', 'expectedAmount'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'booking_id'   => 'required|exists:bookings,id',
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date|before_or_equal:today',
        ]);

        $booking = Booking::with('room')->findOrFail($validatedData['booking_id']);
        $this->authorizeTenantBooking($booking);

        if ($booking->status !== 'pending') {
            return redirect()->route('payments.index')
                             ->with('error', 'Transaksi ditolak. Status booking tidak valid untuk pembayaran.');
        }

        $expectedAmount = $booking->room->price * $booking->duration_months;
        
        if ((int)$validatedData['amount'] !== $expectedAmount) {
            return back()->withInput()
                         ->with('error', "Nominal tidak sesuai. Jumlah yang harus dibayar adalah Rp " . number_format($expectedAmount));
        }

        DB::beginTransaction();

        try {
            $lockedBooking = Booking::with(['room.kost', 'tenant'])->where('id', $booking->id)->lockForUpdate()->first();
            $room = $lockedBooking->room;
            $kost = $room->kost;
            $tenant = $lockedBooking->tenant;

            $startDate = $lockedBooking->start_date;
            $endDate = $startDate->copy()->addMonths($lockedBooking->duration_months);

            $paymentRecord = [
                'transaction_reference' => 'TRX-' . time() . '-BKG-' . $lockedBooking->id,
                
                'payment_channel' => $request->input('payment_method', 'manual_transfer'), 
                
                'snapshots' => [
                    'tenant' => [
                        'name'  => $tenant->name,
                        'email' => $tenant->email,
                    ],
                    'property' => [
                        'kost_name'       => $kost->kost_name,
                        'room_name'       => $room->room_name,
                        'price_per_month' => $room->price, 
                    ],
                ],
                
                'financial_recap' => [
                    'duration_months' => $lockedBooking->duration_months,
                    'base_amount'     => $room->price * $lockedBooking->duration_months,
                    'admin_fee'       => 0, 
                    'total_paid'      => (int) $validatedData['amount'],
                ],
                
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => $endDate->toDateString(),
                ],
                
                'timestamp' => now()->toIso8601String(),
            ];

            $payment = Payment::create([
                'booking_id'     => $lockedBooking->id,
                'amount'         => $validatedData['amount'],
                'payment_date'   => $validatedData['payment_date'],
                'status'         => 'paid',
                'payment_record' => $paymentRecord 
            ]);

            $lockedBooking->update(['status' => 'active']);

            DB::commit();

            return redirect()
                ->route('payments.show', $payment)
                ->with('success', 'Pembayaran berhasil dikonfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Terjadi kegagalan saat memproses pembayaran Anda. Saldo/Booking Anda belum dipotong.');
        }
    }

    public function show(Payment $payment)
    {
        $this->authorizeTenantBooking($payment->booking);
        
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        abort(403, 'Catatan finansial bersifat permanen dan tidak dapat diedit secara manual.');
    }

    public function update(Request $request, Payment $payment)
    {
        abort(403, 'Aksi ilegal.');
    }

    public function destroy(Payment $payment)
    {
        abort(403, 'Catatan finansial tidak dapat dihapus. Hubungi Admin jika terjadi kesalahan sistem.');
    }
}