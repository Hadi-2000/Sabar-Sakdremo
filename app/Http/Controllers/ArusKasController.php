<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\Kas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArus_KasRequest;
use App\Http\Requests\UpdateArus_KasRequest;
use App\Http\Requests\UpdatekasRequest;

class ArusKasController extends Controller
{
    public function index(){
        $arus = ArusKas::all();
        return view('tampilan.keuangan.kas', compact('arus'));
    }
    public function search(Request $request){
        if (session()->has('error')) {
            session()->forget('error');
        }
        
        $query = $request->input('query');
        
        // Cek apakah query hanya berupa angka (kemungkinan pencarian tanggal)
        $isDate = strtotime($query) !== false;
    
        $arus = ArusKas::query();
    
        if (!empty($query)) {
            $arus->where('keterangan', 'LIKE', '%'.$query.'%')
                ->orWhere('jenis_kas', 'LIKE', '%'.$query.'%')
                ->orWhere('jenis_transaksi', 'LIKE', '%'.$query.'%');
    
            // Jika query berupa tanggal, tambahkan filter pada created_at
            if ($isDate) {
                $arus->orWhereDate('created_at', '=', date('Y-m-d', strtotime($query)));
            }
        }
    
        $arus = $arus->get(); // Ambil hasil query
    
        // Jika data kosong, redirect dengan pesan error
        if ($arus->isEmpty()) {
            return redirect()->route('index')->with('error', 'Data tidak ditemukan!');
        }
    
        return view('tampilan.keuangan.kas', compact('arus', 'query'));
    }
    public function create(Request $request) {
        // Validasi input
        $request->validate([
            'keterangan' => 'required|string',
            'jenis_kas' => 'required|string',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah' => 'required|numeric|min:1',
        ]);
    
        // Cek apakah jenis kas ada di database
        $param = Kas::where('jenis_kas', $request->jenis_kas)->first();
        
        if (!$param) {
            return redirect()->route('create')->with('error', 'Jenis Kas tidak ditemukan!');
        }
    
        // Tentukan kategori kas
        $kas = ($param->jenis_kas == 'totalOnHand') ? "OnHand" : "Operasional";
    
        // Hitung saldo baru
        $jumlahFix2 = ($request->jenis_transaksi == 'Masuk')
            ? $param->saldo + $request->jumlah
            : $param->saldo - $request->jumlah;
    
        // Pastikan saldo tidak negatif jika transaksi "Keluar"
        if ($jumlahFix2 < 0) {
            return redirect()->route('create')->with('error', 'Saldo tidak mencukupi untuk transaksi keluar!');
        }
    
        // Update saldo di tabel Kas
        $param->update(['saldo' => $jumlahFix2]);
        $idKas = $param->id;
    
        // Simpan transaksi ke ArusKas
        ArusKas::create([
            'idKas' => $idKas, // Pastikan nama kolom benar
            'keterangan' => $request->keterangan,
            'jenis_kas' => $kas,
            'jenis_transaksi' => $request->jenis_transaksi,
            'jumlah' => $request->jumlah,
        ]);
            
        return redirect()->route('create')->with('success', 'Transaksi berhasil ditambahkan!');
    }
    
    //hapus isi
    public function destroy($id){
        ArusKas::destroy($id);
        return redirect()->route('hapus')->with('success', 'Data berhasil dihapus!');
    }
}
