<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Http\Requests\StoreMesinRequest;
use App\Http\Requests\UpdateMesinRequest;

class MesinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mesin = Mesin::orderBy('nama_mesin')->paginate(10);
        return view('tampilan.penggilingan.mesin.index', compact('mesin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tampilan.penggilingan.mesin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMesinRequest $request)
    {
        $data = $request->validated();

        Mesin::create([
            'nama_mesin' => $data['nama'],
            'merek_mesin' => $data['merek'],
        ]);
        return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mesin $mesin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $mesin = Mesin::find($id);
        if ($mesin === null) {
            return redirect()->route('mesin.index')->with('error', 'Data Mesin Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.mesin.update', compact('mesin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMesinRequest $request, $id)
    {
        $data = $request->validated();
        $mesin = Mesin::find($id);
        if ($mesin === null) {
            return redirect()->route('mesin.index')->with('error', 'Data Mesin Tidak Ditemukan.');
        }
        $mesin->update([
            'nama_mesin' => $data['nama'],
           'merek_mesin' => $data['merek'],
        ]);
        return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Mesin::find($id)->delete();
        return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Dihapus');
    }
}
