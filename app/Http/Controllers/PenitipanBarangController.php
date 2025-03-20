<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Models\PenitipanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StorePenitipanBarangRequest;
use App\Http\Requests\UpdatePenitipanBarangRequest;

class PenitipanBarangController extends Controller
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
            $data = PenitipanBarang::join('pelanggans', 'penitipan_barangs.id_pelanggan', '=', 'pelanggans.id')
            ->select('penitipan_barangs.*', 'pelanggans.nama as nama_pelanggan')
            ->orderBy('pelanggans.nama')
            ->paginate(10);
    
            return view('tampilan.penggilingan.penitipan.penitipan', compact('data'));
        }catch(\PDOException $e){
            Log::error('Error pada PenitipanBarangController@index :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada database');
        }catch(\Exception $e){
            Log::error('Error pada PenitipanBarangController@index :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }

    public function search(Request $request)
{
    try{

        if (session()->has('error')) {
            session()->forget('error');
        }
    
        $query = trim(strtolower($request->input('query', ''))); 
        $data = collect(); // Pastikan $data tidak error jika kosong
    
        if (!empty($query)) {
            $data = PenitipanBarang::join('pelanggans', 'penitipan_barangs.id_pelanggan', '=', 'pelanggans.id')
                ->select('penitipan_barangs.*', 'pelanggans.nama as nama_pelanggan')
                ->where(function($p) use ($query) {
                    $p->where('pelanggans.nama', 'LIKE', ["%{$query}%"])
                      ->orWhere('penitipan_barangs.barang','LIKE', ["%{$query}%"])
                      ->orWhere('penitipan_barangs.jumlah)', 'LIKE', ["%{$query}%"])
                      ->orWhere('penitipan_barangs.status', 'LIKE', ["%{$query}%"]);
                })
                ->orderBy('pelanggans.nama')
                ->paginate(10);
        }
    
        if ($data->isEmpty()) {
            return redirect()->back()->withErrors('Data not found');
        }
    
        return view('tampilan.penggilingan.penitipan.penitipan', compact('data', 'query'));
    }catch(\Exception $e){
        Log::error('Error pada PenitipanBarangController@search :'.$e->getMessage());
        return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function cekPelanggan(Request $request){
        try {
            $request->validate([
                'nama_pelanggan' => 'required|string|min:3',
            ]);
    
            $pelanggan = Pelanggan::where('nama', $request->nama_pelanggan)->first();
    
            return response()->json([
                'ada' => $pelanggan ? true : false,
                'alamat' => $pelanggan ? $pelanggan->alamat : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function cekAuto(Request $request){
        try{
            $query = $request->query('query');
            $pelanggan = DB::table('pelanggan')->where('nama','LIKE','%'.$query.'%')->limit(5)->get(['nama','alamat']);
            return response()->json($pelanggan);
        }catch(\Exception $e){
            Log::error('Error pada PenitipanBarangController@cekAuto :'.$e->getMessage());
            return response()->json(['error' => 'Terdapat kesalahan pada server'], 500);
        }
    }
    
     public function create()
    {
        return view('tampilan.penggilingan.penitipan.penitipan-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenitipanBarangRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $jumlah = str_replace('.', '', $data['jumlah']); // Hapus titik pemisah ribuan
    
            // Simpan atau dapatkan data pelanggan langsung dari firstOrCreate
            $pelanggan = Pelanggan::firstOrCreate(
                ['nama' => $data['nama_pelanggan']],
                [
                    'alamat' => $data['alamat_pelanggan'] ?? '',
                    'no_telepon' => $data['no_telepon'] ?? '',
                ]
            );
    
            // Langsung gunakan $pelanggan->id tanpa query tambahan
            PenitipanBarang::create([
                'id_pelanggan' => $pelanggan->id,
                'barang' => $data['barang'],
                'jumlah' => $jumlah, // Tidak perlu ?? '0', karena selalu ada nilai
                'status' => 'Menitipkan'
            ]);
            DB::commit();     
            return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil disimpan');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada PenitipanBarangController@store :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada database');
        }catch(\Exception $e){
            Log::error('Error pada PenitipanBarangController@store :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit(PenitipanBarang $penitipan)
    {
        try{
            if (!$penitipan) {
                return redirect()->back()->with('error', 'Data penitipan tidak ditemukan');
            }
            $pelanggan = Pelanggan::find($penitipan->id_pelanggan);
            return view('tampilan.penggilingan.penitipan.penitipan-update', compact('penitipan','pelanggan'));
        }catch (\PDOException $e){
            Log::error('Error pada PenitipanBarangController@edit :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada database');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenitipanBarangRequest $request, $id)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $pelanggan = Pelanggan::findOrFail($id);
            $jumlah = str_replace('.','',$data['jumlah']);
            if($pelanggan){
                $penitipan = PenitipanBarang::where('id_pelanggan',$id);
                $penitipan->update([
                    'barang' => $data['barang'],
                    'jumlah' => $jumlah,
                   'status' => $data['status'],
                ]);
        }
            DB::commit();
            return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil diupdate');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada PenitipanBarangController@update :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada database');
        }catch(\Exception $e){
            Log::error('Error pada PenitipanBarangController@update :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenitipanBarang $penitipan)
    {
        try{
            $penitipan->delete();
            return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil dihapus');
        }catch(\PDOException $e){
            Log::error('Error pada PenitipanBarangController@destroy :'.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada database');
        }
    }
}
