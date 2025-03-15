<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Perbaikan;
use Illuminate\Http\Request;
use App\Http\Requests\StorePerbaikanRequest;
use App\Http\Requests\UpdatePerbaikanRequest;

class PerbaikanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perbaikan = Perbaikan::orderBy('teknisi')->paginate(10);
        return view('tampilan.penggilingan.perbaikan.index', compact('perbaikan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mesin = Mesin::orderBy('nama_mesin')->get();
        $perbaikan = Perbaikan::orderBy('teknisi')->get();
        return view('tampilan.penggilingan.perbaikan.create', compact('mesin','perbaikan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerbaikanRequest $request)
{
    $data = $request->validated();

    // Cari mesin berdasarkan nama_mesin
    $mesin = Mesin::where('nama_mesin', $data['mesin'])->first();

    // Cek apakah mesin ditemukan
    if (!$mesin) {
        return back()->with('error', 'Mesin tidak ditemukan. Pastikan nama mesin sesuai.');
    }

    // Pastikan jumlah hanya angka (hapus titik pemisah ribuan jika ada)
    $jumlah = isset($data['jumlah']) ? str_replace('.', '', $data['jumlah']) : 0;

    // Simpan data ke database
    Perbaikan::create([
        'id_mesin' => $mesin->id, // Sekarang dijamin ada
        'teknisi' => $data['nama'],
        'keterangan' => $data['keterangan'],
        'biaya' => $jumlah,
        'status' => $data['status'],
    ]);

    // Redirect ke halaman index dengan pesan sukses
    return redirect()->route('perbaikan.index')->with('success', 'Data perbaikan berhasil ditambahkan.');
}


    /**
     * Display the specified resource.
     */
    public function show(Perbaikan $perbaikan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perbaikan $perbaikan)
    {
        $mesin = Mesin::orderBy('nama_mesin')->get();
        if ($perbaikan === null) {
            return redirect()->route('perbaikan.index')->with('error', 'Data Perbaikan Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.perbaikan.update', compact('mesin', 'perbaikan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerbaikanRequest $request, Perbaikan $perbaikan)
    {
        $data = $request->validated();
        $mesin = Mesin::where('nama_mesin',$data['mesin'])->first();
        $jumlah = str_replace('.','',$data['jumlah']);
        if ($perbaikan === null) {
            return redirect()->route('perbaikan.index')->with('error', 'Data Perbaikan Tidak Ditemukan.');
        }
        $perbaikan->update([
            'id_mesin' => $mesin->id,
            'teknisi' => $data['nama'],
            'keterangan'=> $data['keterangan'],
            'biaya' => $jumlah,
            'status' => $data['status'],
        ]);
        return redirect()->route('perbaikan.index')->with('success', 'Data Perbaikan Berhasil Diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perbaikan = Perbaikan::find($id);
        if ($perbaikan === null) {
            return redirect()->route('perbaikan.index')->with('error', 'Data Perbaikan Tidak Ditemukan.');
        }
        $perbaikan->delete();
        return redirect()->route('perbaikan.index')->with('success', 'Data Perbaikan Berhasil Dihapus.');
    }
    public function search(Request $request){
        $query = $request->query('query');
        if($query){
            // Jika query ada, maka pencarian akan dilakukan di kolom teknisi, keterangan, dan status.
        $perbaikan = Perbaikan::where('teknisi','LIKE',"%{$query}%")
        ->orWhere('keterangan','LIKE',"%{$query}%")
        ->orWhere('status','LIKE',"%{$query}%")
        ->paginate(10);
        return view('tampilan.penggilingan.perbaikan.index', compact('perbaikan'));
        }else{
            $perbaikan = Perbaikan::orderBy('teknisi')->paginate(10);
    
            return view('tampilan.penggilingan.perbaikan.index', compact('perbaikan'));
        }
        if($perbaikan->isEmpty()){
            return redirect()->back()->with('error','Data Perbaikan Kosong');
        }        
}
}