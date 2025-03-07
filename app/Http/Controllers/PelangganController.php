<?php

namespace App\Http\Controllers;

use App\Models\pelanggan;
use Illuminate\Http\Request;
use App\Http\Requests\StorepelangganRequest;
use App\Http\Requests\UpdatepelangganRequest;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::orderBy('nama')->paginate(10);
        return view('tampilan.penggilingan.pelanggan',compact('pelanggan'));
    }

    public function search(Request $request){
        if(session()->has('error')){

            session()->forget('error');
        }
        $query = strtolower($request->input('query'));

        if (!empty($query)) {
            $pelanggan = Pelanggan::where(function($p) use ($query) {
            $p->where('nama', 'like', '%'.$query.'%')
            ->orWhere('alamat', 'like', '%'.$query.'%')
            ->orWhere('no_telepon', 'like', '%'.$query.'%');
            })->orderBy('nama')->paginate(10);

            return view('tampilan.penggilingan.pelanggan',compact('pelanggan','query'));
    }else{
        $pelanggan = Pelanggan::orderBy('nama')->paginate(10);
    }
    if($pelanggan->isEmpty()){
        return redirect()->back()->with('error','Data pelanggan kosong');
    }
}

    public function create()
    {
        return view('tampilan.penggilingan.pelanggan-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorepelangganRequest $request)
    {
        $data = $request->validated();
        $pelanggan = Pelanggan::where('nama',$data['nama'])
        ->orWhere('alamat',$data['alamat'])->first();
        if($pelanggan){
            return back()->with('error', 'Nama dan alamat pelanggan sudah dipakai.');
        }
        Pelanggan::create([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? '',
            'no_telepon' => $data['no_telepon'] ?? '',
        ]);

        return redirect()->route('penggilingan.pelanggan.index')->with('success','Data pelanggan berhasil ditambahkan');
    }

    public function indexUpdate($id){
        $pelanggan = Pelanggan::find($id);
        if ($pelanggan === null) {
            return redirect()->route('penggilingan.pelanggan.index')->with('error', 'Data pelanggan tidak ditemukan.');
        }
        return view('tampilan.penggilingan.pelanggan-update', compact('pelanggan'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function update(UpdatepelangganRequest $request, $id)
    {
        $data = $request->validated();
        $cek = Pelanggan::where('nama',$data['nama'])->orWhere('alamat',$data['alamat'])->first();
        
      if($cek){
            return back()->with('error', 'Nama dan alamat pelanggan sudah dipakai.');
        }
        Pelanggan::find($id)->update([
            'nama' => $data['nama'],
            'alamat' => $data['alamat']?? '',
            'no_telepon' => $data['no_telepon']?? '',
        ]);

        return redirect()->route('penggilingan.pelanggan.index')->with('success','Data pelanggan berhasil diubah');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Pelanggan::find($id)->delete();
        return redirect()->route('penggilingan.pelanggan.index')->with('success','Data pelanggan berhasil dihapus');
    }
}
