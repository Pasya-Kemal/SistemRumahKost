<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    private function getCurrentOwnerId()
    {
        return auth()->id();
    }

    /**
     * Authz owner_id
     */
    private function authorizeRoomOwnership(Room $room)
    {
        if ($room->kost->owner_id !== $this->getCurrentOwnerId()) {
            abort(403, 'Akses ditolak. Kamar ini bukan bagian dari properti Anda.');
        }
    }

    /**
     * show room woner has
     */
    public function index()
    {
        $rooms = Room::whereHas('kost', function ($query) {
            $query->where('owner_id', $this->getCurrentOwnerId());
        })->with('kost')->get();

        return view('owner.rooms.index', compact('rooms'));
    }

    /**
     * show information detail
     */
    public function show(Room $room)
    {
        $this->authorizeRoomOwnership($room);
        $room->load(['kost', 'photos']);

        return view('owner.rooms.show', compact('room'));
    }
}