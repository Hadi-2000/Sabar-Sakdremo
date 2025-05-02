<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAsetRequest;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateAsetRequest;

class AsetController extends Controller
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
        try {
            $produk = Aset::orderBy('nama')->paginate(10);
            foreach ($produk as $p) {
                $p->updated_at = \Carbon\Carbon::parse($p->updated_at)->format('Y-m-d');
            }            
            return view('tampilan.penggilingan.produk.index', compact('produk'));
        } catch (\Exception $e) {
            Log::error('Error pada AsetController@index: ' . $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada kode.');
        }
    }

    /**
     * Search for the specified resource.
     */
    public function search(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        $request->validate([
            'query' => 'nullable|string|min:2|max:255'
        ]);
        
        $query = trim(strtolower(strip_tags($request->validate([
            'query' => 'nullable|string|min:1|max:255'
        ])['query'] ?? '')));
        $isDate = strtotime($query) !== false;

        try{
            if (!empty($query)) {
                $produk = Aset::where('nama', 'LIKE', "%{$query}%")
                ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                ->orWhere('created_at', 'LIKE', "%{$query}%")
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
        }catch(\Exception $e){
            Log::error('Error pada AsetController@search: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada kode.');
        }
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
        try{
            $data = $request->validated();
            $harga_satuan = str_replace('.','',$data['harga_satuan']);

            DB::beginTransaction();
            
            Aset::create([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'satuan' => $data['satuan'],
                'harga_satuan' =>$harga_satuan
            ]);

            DB::commit();

        return redirect()->route('aset.index')->with('success', 'Data Berhasil Ditambahkan.');
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada AsetController@store: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada kode.');
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = Aset::where('id',$id)->first();
        $produk->harga_satuan = number_format($produk->harga_satuan, 0, ',', '.');
        if(!$produk){
            return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.produk.update', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsetRequest $request,Aset $aset)
    {
        try{
            $data = $request->validated();
            if ($aset === null) {
                return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
            }
            $harga_satuan = str_replace('.','',$data['harga_satuan']);
            DB::beginTransaction();
            $aset->update([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'satuan' => $data['satuan'],
                'harga_satuan' =>$harga_satuan
            ]);
            DB::commit();
            return redirect()->route('aset.index')->with('success', 'Data Berhasil Diubah.');
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada AsetController@update: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada kode.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aset $aset)
    {
        try{
            $aset->delete();
            return redirect()->route('aset.index')->with('success', 'Data Berhasil Dihapus.');
        }catch(\Exception $e){
            Log::error('Error pada AsetController@destroy: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada kode.');
        }
    }
}
