<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\Photo;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRoomController extends Controller
{
    private function scopedKostIds(): ?array
    {
        $effective = Auth::user()?->effectiveOwnerId();
        if ($effective === null) return null;
        return Kost::where('owner_id', $effective)->pluck('id')->all();
    }

    private function authorizeKost(int $kostId): void
    {
        $allowed = $this->scopedKostIds();
        if ($allowed === null) return;
        abort_unless(in_array($kostId, $allowed, true), 403,
            'Kost ini bukan milik tim Anda.');
    }

    private function authorizeRoom(Room $room): void
    {
        $allowed = $this->scopedKostIds();
        if ($allowed === null) return;
        abort_unless(in_array((int) $room->kost_id, $allowed, true), 403,
            'Kamar ini bukan milik tim Anda.');
    }

    public function index()
    {
        $query = Room::with('kost');
        if (($ids = $this->scopedKostIds()) !== null) {
            $query->whereIn('kost_id', $ids);
        }
        $rooms = $query->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $kosts = ($ids = $this->scopedKostIds()) === null
            ? Kost::all()
            : Kost::whereIn('id', $ids)->get();

        return view('admin.rooms.create', compact('kosts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kost_id'      => 'required|exists:kosts,id',
            'room_name'    => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'required|string',
            'is_available' => 'boolean',
            'photos.*'     => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $this->authorizeKost((int) $validatedData['kost_id']);
        $validatedData['is_available'] = $request->has('is_available');

        DB::beginTransaction();
        try {
            $room = Room::create([
                'kost_id'      => $validatedData['kost_id'],
                'room_name'    => $validatedData['room_name'],
                'price'        => $validatedData['price'],
                'description'  => $validatedData['description'],
                'is_available' => $validatedData['is_available'],
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    $path = $file->store('room_photos', 'public');
                    Photo::create([
                        'room_id'    => $room->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil ditambahkan ke Kost.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to store room', [
                'user_id' => Auth::id(),
                'kost_id' => $validatedData['kost_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function show(Room $room)
    {
        $this->authorizeRoom($room);
        $room->load(['kost', 'photos']);
        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $this->authorizeRoom($room);
        $kosts = ($ids = $this->scopedKostIds()) === null
            ? Kost::all()
            : Kost::whereIn('id', $ids)->get();

        return view('admin.rooms.edit', compact('room', 'kosts'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorizeRoom($room);

        $validatedData = $request->validate([
            'kost_id'      => 'required|exists:kosts,id',
            'room_name'    => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'required|string',
            'is_available' => 'boolean',
        ]);

        $this->authorizeKost((int) $validatedData['kost_id']);
        $validatedData['is_available'] = $request->has('is_available');

        $room->update($validatedData);

        return redirect()->route('admin.rooms.index')->with('success', 'Data Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $this->authorizeRoom($room);
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }
}
