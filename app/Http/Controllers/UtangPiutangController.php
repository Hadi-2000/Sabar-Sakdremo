<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facedes\view;
use App\Models\Kas;
use App\Models\Pelanggan;
use App\Models\UtangPiutang;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class UtangPiutangController extends Controller
{
    private const VALIDATION_RULE_STRING = 'request|string';
   // Utang
    public function indexUtang()
    {
        $utang = UtangPiutang::orderBy('nama', 'desc')->get();
        return view('tampilan.keuangan.utang', compact('utang'));
    }
    public function searchUtang(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
    
        $query = $request->input('query');
    
        // Jika query kosong, langsung kembali ke index tanpa error
        if (empty($query)) {
            return redirect()->route('keuangan.utang.index');
        }
    
        $isDate = strtotime($query) !== false;
    
        // Cek apakah query terkait dengan jenis "Utang" atau "Piutang" secara langsung
        if (strtolower($query) == 'utang') {
            $utang = UtangPiutang::where('jenis', 'Utang')->orderBy('nama', 'desc')->get();
        } elseif (strtolower($query) == 'piutang') {
            $utang = UtangPiutang::where('jenis', 'Piutang')->orderBy('nama', 'desc')->get();
        } else {
            $utang = UtangPiutang::all(); // Default hanya mencari "Utang"
    
            $utang->where(function($q) use ($query, $isDate) {
                $q->where('keterangan', 'LIKE', '%'.$query.'%')
                  ->orWhere('nama', 'LIKE', '%'.$query.'%')
                  ->orWhere('alamat', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis', 'LIKE', '%'.$query.'%')
                  ->orWhere('nominal', 'LIKE', '%'.$query.'%')
                  ->orWhere('status', 'LIKE', '%'.$query.'%')
                  ->orWhere('updated_at', 'LIKE', '%'.$query.'%');
    
                if ($isDate) {
                    $q->orWhereDate('updated_at', '=', date('Y-m-d', strtotime($query)));
                }
            });
    
            $utang = $utang->orderBy('nama', 'desc')->get();
        }
    
        // Jika data tidak ditemukan, kembali ke halaman utama dengan error
        if ($utang->isEmpty()) {
            return redirect()->route('keuangan.utang.index')->with('error', 'Data Tidak Ada.');
        }
    
        return view('tampilan.keuangan.utang', compact('utang', 'query'));
    }
    

    public function createIndexUtang(){
        return view('tampilan.keuangan.utang-create');
    }
    public function create(Request $request){
        $request->validate([
            'nama_pelanggan' => self::VALIDATION_RULE_STRING,
            'alamat_pelanggan' => self::VALIDATION_RULE_STRING,
            'no_telepon' => 'numeric',
            'jumlah_hidden' => 'required|numeric|min:1',
            'jenis' =>self::VALIDATION_RULE_STRING,
           'status' => self::VALIDATION_RULE_STRING
        ]);
        $jumlah = str_replace('.','',$request->jumlah_hidden);

        $utang = Kas::where('jenis_kas','Utang')->first();
        $piutang = Kas::where('jenis_kas','Piutang')->first();
        $totalAsset = Kas::where('jenis_kas','totalAsset')->first();
        $pelanggan = Pelanggan::where('nama',$request->nama_pelanggan)->first();
    
        $utang_fix = $utang->saldo + $jumlah;
        $totalAsset_fix = $totalAsset->saldo - $jumlah;

        if(!$pelanggan){
            Pelanggan::create([
                'nama' => $request->nama_pelanggan,
                'alamat' => $request->alamat_pelanggan,
                'no_telepon' => ''
            ]);
        }

        if($request->jenis == 'Utang'){
            UtangPiutang::create([
                'id_pelanggan' => $pelanggan->id,
                'nama' => $request->nama_pelanggan,
                'alamat' => $request->alamat_pelanggan,
                'keterangan' => $request->keterangan,
                'jenis' => 'Utang',
                'nominal' => $jumlah,
               'status' => $request->status
            ]);
            $utang->update(['saldo'=>$utang_fix]);
            $totalAsset->update(['saldo'=>$totalAsset_fix]);
            return view('tampilan.keuangan.utang');
        }
        elseif($request->jenis =='Piutang'){
            UtangPiutang::create([
                'id_pelanggan' => $pelanggan->id,
                'nama' => $request->nama_pelanggan,
                'alamat' => $request->alamat_pelanggan,
                'keterangan' => $request->keterangan,
                'jenis' => 'Piutang',
                'nominal' => $jumlah,
               'status' => $request->status
            ]);
            $piutang->update(['saldo'=>$utang_fix]);
            $totalAsset->update(['saldo'=>$totalAsset_fix]);
            return view('tampilan.keuangan.utang');
        }
    }

    //cek Pelanggan ada tidak
    public function checkPelanggan(Request $request) {
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
    
}