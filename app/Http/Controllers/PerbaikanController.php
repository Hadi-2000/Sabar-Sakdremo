<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use App\Models\Perbaikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StorePerbaikanRequest;
use App\Http\Requests\UpdatePerbaikanRequest;

class PerbaikanController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $perbaikan = Perbaikan::orderBy('teknisi')->paginate(10);
            return view('tampilan.penggilingan.perbaikan.index', compact('perbaikan'));
        }catch(\Exception $e){
            Log::error('Error pada PerbaikanController@index'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try{
            $mesin = Mesin::orderBy('nama_mesin')->get();
            $perbaikan = Perbaikan::orderBy('teknisi')->get();
            return view('tampilan.penggilingan.perbaikan.create', compact('mesin','perbaikan'));
        }catch(\Exception $e){
            Log::error('Error pada PerbaikanController@create'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }catch(\PDOException $e){
            Log::error('Error pada PerbaikanController@create'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerbaikanRequest $request)
{
    try{
        DB::beginTransaction();
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
        DB::commit();
    
        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('perbaikan.index')->with('success', 'Data perbaikan berhasil ditambahkan.');
    }catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error pada PerbaikanController@store'.$e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
    }catch(\PDOException $e){
        DB::rollBack();
        Log::error('Error pada PerbaikanController@store'.$e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
    }
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
        try {
            $mesin = Mesin::orderBy('nama_mesin')->get();
            return view('tampilan.penggilingan.perbaikan.update', compact('mesin', 'perbaikan'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Data Perbaikan tidak ditemukan: ' . $e->getMessage());
            return redirect()->route('perbaikan.index')->with('error', 'Data Perbaikan Tidak Ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error pada PerbaikanController@edit: ' . $e->getMessage());
            return redirect()->route('perbaikan.index')->with('error', 'Terjadi kesalahan pada server.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerbaikanRequest $request, Perbaikan $perbaikan)
    {
        try{
            DB::beginTransaction();
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
            DB::commit();
            return redirect()->route('perbaikan.index')->with('success', 'Data Perbaikan Berhasil Diubah.');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PerbaikanController@update'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada PerbaikanController@update'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $perbaikan = Perbaikan::find($id);
            if ($perbaikan === null) {
                return redirect()->route('perbaikan.index')->with('error', 'Data Perbaikan Tidak Ditemukan.');
            }
            $perbaikan->delete();
            return redirect()->route('perbaikan.index')->with('success', 'Data Perbaikan Berhasil Dihapus.');
        }catch(\Exception $e){
            Log::error('Error pada PerbaikanController@destroy'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }catch(\PDOException $e){
            Log::error('Error pada PerbaikanController@destroy'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }
    }
    public function search(Request $request){
        try{
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));

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
        }catch(\Exception $e){
            Log::error('Error pada PerbaikanController@search'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }catch(\PDOException $e){
            Log::error('Error pada PerbaikanController@search'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server, silakan coba lagi.');
        }
}
}