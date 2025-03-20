<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\pelanggan;
use App\Models\UtangPiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class UtangController extends Controller
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
            $utang = UtangPiutang::where('jenis','Utang')->orderBy('nama')->paginate(10);
            return view('tampilan.keuangan.utang.index', compact('utang'));
        }catch(\Exception $e){
            Log::error('Error pada UtangConttroller@index: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }
    }

    public function search(Request $request){
        try{
            if(session()->has('error')){
                session()->forget('error');
            }
            $query = $request->query('query');
            // Implement your search logic here
            if(empty($query)){
                $utang = UtangPiutang::where('jenis','Utang')->orderBy('nama')->paginate(10);
            }else{
                $utang = UtangPiutang::where('nama','LIKE',"%{$query}%")
                                ->orWhere('keterangan','LIKE',"%{$query}%")
                                ->orWhere('alamat', 'LIKE', "%{$query}%")
                                ->orWhere('nominal', 'LIKE', "%{$query}%")
                                ->orWhere('status', 'LIKE', "%{$query}%")
                                ->where('jenis','Utang')
                                ->orderBy('nama')
                                ->paginate(10);
            }
    
            return view('tampilan.keuangan.utang.index', compact('utang','query'));
        }catch(\Exception $e){
            Log::error('Error pada UtangConttroller@store: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }catch(\PDOException $e){
            Log::error('Error pada UtangConttroller@store: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tampilan.keuangan.utang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUtangPiutangRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $data['jumlah'] = str_replace('.','',$data['jumlah']);
    
            $param = pelanggan::where('nama',$data['nama_pelanggan'])->first();
            if(!$param){
                pelanggan::create([
                    'nama' => $data['nama_pelanggan'],
                    'alamat' => $data['alamat_pelanggan'],
                ]);
            }
            $param = pelanggan::where('nama',$data['nama_pelanggan'])->first();
    
            UtangPiutang::create([
                'id_pelanggan' => $param->id,
                'nama' => $data['nama_pelanggan'],
                'alamat' => $data['alamat_pelanggan'],
                'keterangan' => $data['keterangan'],
                'ambil' => $data['ambil'],
                'jenis' => 'Utang',
                'nominal' => $data['jumlah'],
               'status' => $data['status'],
            ]);
            DB::commit();
            
            return redirect()->route('utang.index')->with('success', 'Data utang berhasil ditambahkan');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada UtangConttroller@store: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada UtangConttroller@store: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $utang = UtangPiutang::find($id);
        return view('tampilan.keuangan.utang.update', compact('utang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtangPiutangRequest $request, $id)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $data['jumlah'] = str_replace('.','',$data['jumlah']);
    
            $utang = UtangPiutang::find($id);
            if($utang){
                $utang->update([
                    'keterangan' => $data['keterangan'],
                    'ambil' => $data['ambil'],
                    'jenis' => $data['jenis'],
                    'nominal' => $data['jumlah'],
                    'status' => $data['status'],
                ]);
                DB::commit();
                return redirect()->route('utang.index')->with('success', 'Data utang berhasil diubah');
            } else {
                return redirect()->route('utang.index')->with('error', 'Data utang tidak ditemukan.');
            }
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada UtangConttroller@update: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }catch(\PDOException $e){
            DB::rollBack();
            Log::error('Error pada UtangConttroller@update: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $utang = UtangPiutang::find($id);
            if ($utang === null) {
                return redirect()->route('utang.index')->with('error', 'Data Utang Tidak Ditemukan.');
            }
            $utang->delete();
            return redirect()->route('utang.index')->with('success', 'Data Utang Berhasil Dihapus.');
        }catch(\Exception $e){
            Log::error('Error pada UtangConttroller@destroy: '.$e->getMessage());
            return redirect()->route('utang.index')->with('error', 'Terjadi kesalahan, coba lagi nanti');
        }
    }
}
