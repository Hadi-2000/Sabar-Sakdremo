<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAsetRequest;
use App\Http\Requests\UpdateAsetRequest;

class AsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Aset::orderBy('nama')->paginate(10);
        return view('tampilan.penggilingan.produk.index',compact('produk'));
    }

    /**
     * Search for the specified resource.
     */
    public function search(Request $request){
        if(session()->has('error')){

            session()->forget('error');
        }
        $query = strtolower($request->input('query'));
        $isDate = strtotime($query) !== false;


        if (!empty($query)) {
            $produk = Aset::where('nama', 'LIKE', '%'.$query.'%')
            ->orWhere('deskripsi', 'LIKE', '%'.$query.'%')
            ->orWhere('jumlah', 'LIKE', '%'.$query.'%')
            ->orWhere('created_at', 'LIKE', '%'.$query.'%')
            ->orderBy('nama')
            ->paginate(10);
            
            if ($isDate && strtotime($query) !== false) {
                $produk = Aset::whereDate('created_at', '=', date('Y-m-d', strtotime($query)))
                ->orderBy('nama')
                ->paginate(10);
            }
            
        } else {
            $produk = Aset::orderBy('nama')->paginate(10);
        }
        if ($produk->isEmpty()) {
            return redirect()->route('aset.index')->with('error', 'Data Tidak Ada.');
        }

        return view('tampilan.penggilingan.produk.index', compact('produk','query'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tampilan.penggilingan.produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAsetRequest $request)
    {
        $data = $request->validated();
        $jumlah = str_replace('.','',$data['jumlah']);
        Aset::create([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'],
            'jumlah' => $jumlah,
            'satuan' => $data['satuan'],
            'harga_satuan' =>$data['harga_satuan']
        ]);
        return redirect()->route('aset.index')->with('success', 'Data Berhasil Ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aset $produk)
    {
        if ($produk === null) {
            return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.produk.update', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsetRequest $request,Aset $aset)
    {
        $data = $request->validated();
        if ($aset === null) {
            return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
        }
        $jumlah = str_replace('.','',$data['jumlah']);
        $aset->update([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'],
            'jumlah' => $jumlah,
            'satuan' => $data['satuan'],
            'harga_satuan' =>$data['harga_satuan']
        ]);
        return redirect()->route('aset.index')->with('success', 'Data Berhasil Diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aset $aset)
    {
        if ($aset === null) {
            return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
        }
        $aset->delete();
        return redirect()->route('aset.index')->with('success', 'Data Berhasil Dihapus.');
    }
}
