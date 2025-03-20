<?php

namespace App\Http\Controllers;

use App\Models\pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StorepelangganRequest;
use App\Models\Pelanggan as ModelsPelanggan;
use App\Http\Requests\UpdatepelangganRequest;

class PelangganController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    public function index()
    {
        try{
            $pelanggan = Pelanggan::orderBy('nama')->paginate(10);
            return view('tampilan.penggilingan.pelanggan.pelanggan',compact('pelanggan'));
        }catch(\Exception $e){
            Log::error('Error pada PelangganController@index : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }
    }

    public function search(Request $request){
        try{

            if(session()->has('error')){
                session()->forget('error');
            }
            $query = strtolower($request->input('query'));
        
            if (!empty($query)) {
                $pelanggan = Pelanggan::where(function($p) use ($query) {
                $p->where('nama', 'like', "%{$query}%")
                ->orWhere('alamat', 'like', "%{$query}%")
                ->orWhere('no_telepon', 'like', "%{$query}%");
                })->orderBy('nama')->paginate(10);
            }else{
                $pelanggan = Pelanggan::orderBy('nama')->paginate(10);
            }
            if($pelanggan->isEmpty()){
                return redirect()->back()->with('error','Data pelanggan kosong');
            }
            return view('tampilan.penggilingan.pelanggan.pelanggan',compact('pelanggan','query'));
        }catch(\Exception $e){
            Log::error('Error pada PelangganController@store : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }
}

    public function create()
    {
        return view('tampilan.penggilingan.pelanggan.pelanggan-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorepelangganRequest $request)
    {
        try{
            DB::beginTransaction();
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
            DB::commit();
            return redirect()->route('pelanggan.index')->with('success','Data pelanggan berhasil ditambahkan');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PelangganController@store : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }
    }

    public function edit(Pelanggan $pelanggan){
        if ($pelanggan === null) {
            return redirect()->route('pelanggan.index')->with('error', 'Data pelanggan tidak ditemukan.');
        }
        return view('tampilan.penggilingan.pelanggan.pelanggan-update', compact('pelanggan'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function update(UpdatepelangganRequest $request, Pelanggan $pelanggan)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $cek = Pelanggan::where('id', '!=', $pelanggan->id)
            ->where(function ($query) use ($data) {
                $query->where('nama', $data['nama'])
                    ->orWhere('alamat', $data['alamat']);
            })->first();
    
          if($cek){
                return back()->with('error', 'Nama dan alamat pelanggan sudah dipakai.');
            }
            $pelanggan->update([
                'nama' => $data['nama'],
                'alamat' => $data['alamat']?? '',
                'no_telepon' => $data['no_telepon']?? '',
            ]);
            DB::commit();
            return redirect()->route('pelanggan.index')->with('success','Data pelanggan berhasil diubah');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada PelangganController@update : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PelangganController@destroy : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        try{
            if ($pelanggan === null) {
                return redirect()->route('pelanggan.index')->with('error', 'Data pelanggan tidak ditemukan.');
            }
            $pelanggan->delete();
            return redirect()->route('pelanggan.index')->with('success','Data pelanggan berhasil dihapus');
        }catch(\PDOException $e){
            Log::error('Error pada PelangganController@destroy : '.$e->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan pada server');
        }
    }
}
