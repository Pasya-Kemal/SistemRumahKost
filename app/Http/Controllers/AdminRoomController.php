<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Kost;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminRoomController extends Controller
{
    public function index()
    {
        //admin read
        $rooms = Room::with('kost')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        // Admin create list
        $kosts = Kost::all(); 
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
            'photos.*'     => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $validatedData['is_available'] = $request->has('is_available') ? true : false;

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
                        'is_primary' => $index === 0 ? true : false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil ditambahkan ke Kost.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function show(Room $room)
    {
        $room->load(['kost', 'photos']);
        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $kosts = Kost::all();
        return view('admin.rooms.edit', compact('room', 'kosts'));
    }

    public function update(Request $request, Room $room)
    {
        $validatedData = $request->validate([
            'kost_id'      => 'required|exists:kosts,id',
            'room_name'    => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'description'  => 'required|string',
            'is_available' => 'boolean',
        ]);

        $validatedData['is_available'] = $request->has('is_available') ? true : false;
        $room->update($validatedData);

        return redirect()->route('admin.rooms.index')->with('success', 'Data Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }
}