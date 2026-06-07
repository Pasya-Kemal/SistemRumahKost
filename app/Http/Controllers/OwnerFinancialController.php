<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class OwnerFinancialController extends Controller
{
    private function getCurrentOwnerId()
    {
        return auth()->id();
    }

    /**
     * Show Financial Record
     */
    public function index(Request $request)
    {
        $ownerId = $this->getCurrentOwnerId();

        // Payment -> Booking -> Room -> Kost -> Owner
        $paymentsQuery = Payment::whereHas('booking.room.kost', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        });

        // mont filtering
        if ($request->has('month') && $request->has('year')) {
            $paymentsQuery->whereMonth('payment_date', $request->month)
                          ->whereYear('payment_date', $request->year);
        }
        $payments = $paymentsQuery->with(['booking.room.kost', 'booking.tenant'])
                                  ->orderBy('payment_date', 'desc')
                                  ->get();

        // count Total 'paid'
        $totalRevenue = $payments->where('status', 'paid')->sum('amount');

        return view('owner.financials.index', compact('payments', 'totalRevenue'));
    }

}
