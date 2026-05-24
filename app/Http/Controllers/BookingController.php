<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * TODO: Delete function before merge
     */
    private function getCurrentTenantId()
    {
        return 2; 
    }

    /**
     * Authz Tenant
     */
    private function authorizeTenant(Booking $booking)
    {
        if ($booking->tenant_id !== $this->getCurrentTenantId()) {
            abort(403, 'Akses ditolak. Ini bukan data booking Anda.');
        }
    }

    public function index()
    {
        // Tenant read booking record
        $bookings = Booking::where('tenant_id', $this->getCurrentTenantId())
                           ->with('room.kost') 
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * form booking, get response from booking buttoon in room page
     */
    public function create(Request $request)
    {
        $request->validate(['room_id' => 'required|exists:rooms,id']);
        
        $room = Room::findOrFail($request->room_id);

        if (!$room->is_available) {
            return redirect()->back()->with('error', 'Maaf, kamar ini saat ini tidak tersedia.');
        }

        return view('bookings.create', compact('room'));
    }

    public function store(Request $request)
    {
        // validating input
        $validatedData = $request->validate([
            'room_id'         => 'required|exists:rooms,id',
            'start_date'      => 'required|date|after_or_equal:today',
            'duration_months' => 'required|integer|min:1|max:12', 
        ]);

        $room = Room::findOrFail($validatedData['room_id']);

        // validating rooms availablity
        if (!$room->is_available) {
            return redirect()->route('rooms.show', $room->id)
                             ->with('error', 'Kamar sudah di-booking oleh orang lain.');
        }

        // validating booking attempts
        $existingPendingBooking = Booking::where('tenant_id', $this->getCurrentTenantId())
                                         ->where('room_id', $room->id)
                                         ->where('status', 'pending')
                                         ->exists();

        if ($existingPendingBooking) {
            return redirect()->route('bookings.index')
                             ->with('error', 'Anda sudah memiliki tagihan pending untuk kamar ini. Selesaikan pembayaran terlebih dahulu.');
        }

        // 4. execute Create Booking
        $booking = Booking::create([
            'tenant_id'       => $this->getCurrentTenantId(),
            'room_id'         => $room->id,
            'start_date'      => $validatedData['start_date'],
            'duration_months' => $validatedData['duration_months'],
            'status'          => 'pending', // Hardcode di controller, jangan letakkan di form HTML
        ]);

        // redirect to payment 
        return redirect()->route('payments.create', ['booking_id' => $booking->id])
                         ->with('success', 'Booking berhasil dibuat. Segera lakukan pembayaran untuk mengamankan kamar Anda.');
    }

    public function show(Booking $booking)
    {
        $this->authorizeTenant($booking);
        $booking->load(['room.kost', 'payments']); // show payment record detail

        return view('bookings.show', compact('booking'));
    }

    /**
     * blocking edit access.
     * #must resubmit for edit date
     */
    public function edit(Booking $booking)
    {
        abort(403, ' Silakan batalkan Booking dan buat baru.');
    }

    public function update(Request $request, Booking $booking)
    {
        abort(403, '-');
    }

    /**
     * Cancel Booking (Destroy)
     */
    public function destroy(Booking $booking)
    {
        $this->authorizeTenant($booking);

        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.index')
                             ->with('error', 'Anda hanya dapat membatalkan booking yang berstatus pending.');
        }

        $booking->delete();

        return redirect()->route('bookings.index')
                         ->with('success', 'Booking berhasil dibatalkan.');
    }
}