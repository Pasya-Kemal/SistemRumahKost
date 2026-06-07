<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user      = $request->user()->load('role');
        $roleName  = $user->role?->role_name ?? 'tenants';
        $role      = match ($roleName) {
            'admin' => 'admin',
            'owner' => 'owner',
            default => 'tenant',
        };
        $roleLabel = match ($role) {
            'admin'  => 'Admin',
            'owner'  => 'Owner',
            default  => 'Tenant',
        };

        $payload = compact('user', 'role', 'roleLabel');

        if ($role === 'owner') {
            $payload += $this->ownerData($user->id);
        } elseif ($role === 'admin') {
            $payload += $this->adminData($user->effectiveOwnerId());
        } else {
            $payload += $this->tenantData($user->id);
        }

        return view('dashboard', $payload);
    }

    private function ownerData(int $ownerId): array
    {
        $kostIds = Kost::where('owner_id', $ownerId)->pluck('id');

        $rooms = Room::whereIn('kost_id', $kostIds);
        $revenueMonth = Payment::whereHas('booking.room', fn ($q) => $q->whereIn('kost_id', $kostIds))
            ->where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $recentBookings = Booking::whereHas('room', fn ($q) => $q->whereIn('kost_id', $kostIds))
            ->with(['room.kost', 'tenant'])
            ->latest()
            ->take(5)
            ->get();

        return [
            'stats' => [
                'kosts'        => $kostIds->count(),
                'rooms'        => (clone $rooms)->count(),
                'available'    => (clone $rooms)->where('is_available', true)->count(),
                'revenueMonth' => (int) $revenueMonth,
            ],
            'recentBookings' => $recentBookings,
        ];
    }

    private function adminData(?int $scopeOwnerId): array
    {
        $ownerRoleId = Role::where('role_name', 'owner')->value('id');

        // If admin has a creator owner, scope stats to that owner's kosts.
        if ($scopeOwnerId !== null) {
            $kostIds  = Kost::where('owner_id', $scopeOwnerId)->pluck('id');
            $roomsQ   = Room::whereIn('kost_id', $kostIds);

            $recentRooms = Room::with('kost')
                ->whereIn('kost_id', $kostIds)
                ->latest()
                ->take(5)
                ->get();

            return [
                'stats' => [
                    'owners'   => 1, // the owner this admin reports to
                    'kosts'    => $kostIds->count(),
                    'rooms'    => (clone $roomsQ)->count(),
                    'occupied' => (clone $roomsQ)->where('is_available', false)->count(),
                ],
                'recentRooms' => $recentRooms,
                'adminScope'  => 'team', // for view: scoped to one owner's team
            ];
        }

        // Super admin (seeded): platform-wide stats.
        $recentRooms = Room::with('kost')->latest()->take(5)->get();

        return [
            'stats' => [
                'owners'   => User::where('role_id', $ownerRoleId)->count(),
                'kosts'    => Kost::count(),
                'rooms'    => Room::count(),
                'occupied' => Room::where('is_available', false)->count(),
            ],
            'recentRooms' => $recentRooms,
            'adminScope'  => 'platform',
        ];
    }

    private function tenantData(int $tenantId): array
    {
        $availableRooms = Room::with(['kost', 'photos'])
            ->where('is_available', true)
            ->orderByDesc('created_at')
            ->paginate(6, ['*'], 'page')
            ->withQueryString();

        $activeBooking = Booking::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'pending'])
            ->with(['room.kost'])
            ->latest()
            ->first();

        // Popular cities derived from kost addresses (best-effort: last comma segment).
        $cities = Kost::pluck('address')
            ->map(fn ($addr) => trim((string) last(explode(',', $addr))))
            ->filter()
            ->unique()
            ->values()
            ->take(6);

        return [
            'availableRooms' => $availableRooms,
            'activeBooking'  => $activeBooking,
            'popularCities'  => $cities,
        ];
    }
}
