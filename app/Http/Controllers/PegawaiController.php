<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;

class PegawaiController extends Controller
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
            $pegawai = Pegawai::orderBy('nama')->paginate(10);
            return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja',compact('pegawai'));
        }catch(\Exception $e){
            Log::error('Error pada PegawaiController@index : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
        
    }

    public function search(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        try{
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));

            if (!empty($query)) {
                $pegawai = Pegawai::where(function($p) use ($query) {
                    $p->where('nama', 'like', "%{$query}%")
                    ->orWhere('status', 'like', "%{$query}%")
                    ->orWhere('alamat', 'like', "%{$query}%")
                    ->orWhere('kehadiran', 'like', "%{$query}%")
                    ->orWhere('gaji', 'like', "%{$query}%");
                })->orderBy('nama')->paginate(10);
            }else{
                $pegawai = Pegawai::orderBy('nama')->paginate(10);
            }
            if($pegawai->isEmpty()) {
                return redirect()->back()->withErrors('Data not found');
            }
            return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja',compact('pegawai','query'));
        }catch(\Exception $e){
            Log::error('Error pada PegawaiController@search : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
        
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePegawaiRequest $request)
    {
        try{
            $data = $request->validated();
            $param = Pegawai::where('nama',$data['nama'])
            ->Where('alamat',$data['alamat'])->first();

            $jumlah = str_replace('.','',$data['jumlah_hidden']);
            DB::beginTransaction();
            if($param){
                return back()->with('error','Data Pegawai Sudah Ada');
            }else{
                Pegawai::create([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'no_telp' => $data['no_telp'] ?? '',
                    'status' => 'Aktif',
                    'kehadiran' => 'Tidak Hadir',
                    'gaji' => $jumlah,
                    ]);
            }
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Ditambahkan');    
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@store : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja-update', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        try{

            $data = $request->validated();
            $cek = Pegawai::where('id','!=',$pegawai->id)
            ->where('nama',$data['nama'])
            ->where('alamat',$data['alamat'])->first();

            DB::beginTransaction();

            if($cek){
                return back()->with('error','Data Pegawai Sudah Ada');
            }
            $jumlah = str_replace('.','',$data['jumlah_hidden']);
            $pegawai->update([
                'nama' => $data['nama'],
                'alamat' => $data['alamat'],
                'no_telp' => $data['no_telp']?? '',
                'gaji' => $jumlah
            ]);
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@update : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        try{
            $pegawai->delete();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Dihapus');
        }catch(\Exception $e){
            Log::error('Error pada PegawaiController@destroy : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }
     public function hadir(Pegawai $pegawai){
        try{
            DB::beginTransaction();
            $pegawai->update([
                'kehadiran' => 'Hadir'
            ]);
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@hadir : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
     }
     public function tidakHadir(Pegawai $pegawai){
        try{
            DB::beginTransaction();
            $pegawai->update([
                'kehadiran' => 'Pulang'
            ]);
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@tidakHadir : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
}
}