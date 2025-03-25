<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreMesinRequest;
use App\Http\Requests\UpdateMesinRequest;

use function PHPUnit\Framework\isEmpty;

class MesinController extends Controller
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
            $mesin = Mesin::orderBy('nama_mesin')->paginate(10);
            return view('tampilan.penggilingan.mesin.index', compact('mesin'));
        }catch(\Exception $e){
            Log::error('Error pada MesinController@index :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
        
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
        try{
            $data = $request->validated();
            DB::beginTransaction();
            Mesin::create([
                'nama_mesin' => $data['nama'],
                'merek_mesin' => $data['merek'],
            ]);
            DB::commit();
            return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Ditambahkan');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada MesinController@store :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
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
    public function edit(Mesin $mesin)
    {
        if ($mesin === null) {
            return redirect()->route('mesin.index')->with('error', 'Data Mesin Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.mesin.update', compact('mesin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMesinRequest $request, Mesin $mesin)
    {
        try{
            $data = $request->validated();
            if ($mesin === null) {
                return redirect()->route('mesin.index')->with('error', 'Data Mesin Tidak Ditemukan.');
            }
            DB::beginTransaction();
            $mesin->update([
                'nama_mesin' => $data['nama'],
            'merek_mesin' => $data['merek'],
            ]);
            DB::commit();
            return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada MesinController@update :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mesin $mesin)
    {
        try{
            $mesin->delete();
            return redirect()->route('mesin.index')->with('success', 'Data Mesin Berhasil Dihapus');
        }catch(\Exception $e){
            Log::error('Error pada MesinController@destroy :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
    }
    public function search(Request $request){
        try{
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));
        if(empty($query)){
            $mesin = Mesin::orderBy('nama_mesin')->paginate(10);
        }else{
            $mesin = Mesin::where('nama_mesin', 'LIKE', "%{$query}%")
            ->orWhere('merek_mesin','LIKE',"%{$query}%")
            ->orderBy('merek_mesin')
            ->paginate(10);
        }
        if(!$mesin){
            return redirect()->route('mesin.index')->with('error', 'Data Mesin Tidak Ditemukan');
        } 
        return view('tampilan.penggilingan.mesin.index', compact('mesin'));
        }catch(\Exception $e){
            Log::error('Error pada MesinController@search :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
    }
}
