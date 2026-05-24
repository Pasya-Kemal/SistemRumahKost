<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KostController extends Controller
{
    /**
     * TODO: Delete function before merge
     */
    private function getCurrentOwnerId()
    {
        return 1; 
    }

    /**
     * validate Authz
     */
    private function authorizeOwnership(Kost $kost)
    {
        if ($kost->owner_id !== $this->getCurrentOwnerId()) {
            abort(403, 'Anda tidak memiliki otoritas terhadap Kost ini.');
        }
    }

    /**
     * Display owner list kosts.
     */
    public function index()
    {
        $ownerId = $this->getCurrentOwnerId();
        $kosts = Kost::where('owner_id', $ownerId)->get();

        return view('kosts.index', compact('kosts'));
    }

    /**
     * form for creating a new resource.
     */
    public function create()
    {
        return view('kosts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate input
        $validatedData = $request->validate([
            'kost_name'   => 'required|string|max:255',
            'kost_gender' => 'required|in:Pria,Wanita,Campuran', // Sesuaikan dengan opsi form-mu
            'address'     => 'required|string',
        ]);

        // get owner_id
        $validatedData['owner_id'] = $this->getCurrentOwnerId();

        // create validated owner_id
        Kost::create($validatedData);

        // 4. Return Message
        return redirect()->route('kosts.index')
                         ->with('success', 'Data Kost berhasil ditambahkan.');
    }

    /**
     * Display Data.
     */
    public function show(Kost $kost)
    {
        $this->authorizeOwnership($kost);
        
        // Load relasi room list
        $kost->load('rooms'); 

        return view('kosts.show', compact('kost'));
    }

    /**
     * form editing Data
     */
    public function edit(Kost $kost)
    {
        $this->authorizeOwnership($kost);

        return view('kosts.edit', compact('kost'));
    }

    /**
     * Update  Data
     */
    public function update(Request $request, Kost $kost)
    {
        $this->authorizeOwnership($kost);

        $validatedData = $request->validate([
            'kost_name'   => 'required|string|max:255',
            'kost_gender' => 'required|in:Pria,Wanita,Campuran',
            'address'     => 'required|string',
        ]);

        $kost->update($validatedData);

        return redirect()->route('kosts.index')
                         ->with('success', 'Data Kost berhasil diperbarui.');
    }

    /**
     * Remove Data.
     */
    public function destroy(Kost $kost)
    {
        $this->authorizeOwnership($kost);

        // soft deletes
        $kost->delete();

        return redirect()->route('kosts.index')
                         ->with('success', 'Data Kost berhasil dihapus.');
    }
}