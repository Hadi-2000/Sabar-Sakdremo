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
        $query = $request->input('query');
        $arus = ArusKas::where('keterangan', 'LIKE', '%'.$query.'%')
        ->orWhere('tanggal', 'LIKE', '%'.$query.'%')
        ->orWhere('jenis_kas', 'LIKE', '%'.$query.'%')
        ->orWhere('jenis_transaksi', 'LIKE', '%'.$query.'%')->get();

        return view('tampilan.keuangan.kas', compact('arus','query'));
    }
    public function create(Request $request) {
        $today = now()->format('YYYY-MM-DD');

        $request->validate([
            'keterangan' => 'required|string',
            'jenis_kas' => 'required|string',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $param = Kas::where('jenis_kas', $request->jenis_kas)->firstOrFail();

        // Tentukan kategori kas
        $kas = ($param->jenis_kas == 'totalOnHand') ? "OnHand" : "Operasional";

        // Hitung saldo baru
        $jumlahFix2 = ($request->jenis_transaksi == 'Masuk')
            ? $param->saldo + $request->jumlah
            : $param->saldo - $request->jumlah;

        // Update saldo di tabel Kas
        $param->update(['saldo' => $jumlahFix2]);
        $idKas = $param->id;

        // Simpan transaksi ke ArusKas
        ArusKas::create([
            'idKas' => $idKas,
            'tanggal' => $today,
            'keterangan' => $request->keterangan,
            'jenis_kas' => $kas,
            'jenis_transaksi' => $request->jenis_transaksi,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('arus-kas.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }
}
