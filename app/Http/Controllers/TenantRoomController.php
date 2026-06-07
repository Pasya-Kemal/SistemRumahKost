<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class TenantRoomController extends Controller
{
    /**
     * Browse available rooms with full-text search across room name,
     * description, kost name, and kost address. Supports gender filter,
     * location filter (city) and paginated output for the Tenant search page.
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $gender   = $request->query('gender');
        $location = trim((string) $request->query('location', ''));

        $rooms = Room::query()
            ->with(['kost', 'photos'])
            ->where('is_available', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('room_name', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%")
                      ->orWhereHas('kost', function ($k) use ($q) {
                          $k->where('kost_name', 'LIKE', "%{$q}%")
                            ->orWhere('address', 'LIKE', "%{$q}%");
                      });
                });
            })
            ->when($gender, function ($query) use ($gender) {
                $query->whereHas('kost', fn ($k) => $k->where('kost_gender', $gender));
            })
            ->when($location !== '', function ($query) use ($location) {
                $query->whereHas('kost', fn ($k) => $k->where('address', 'LIKE', "%{$location}%"));
            })
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        return view('tenant.rooms.index', [
            'rooms'    => $rooms,
            'q'        => $q,
            'location' => $location,
        ]);
    }
}
