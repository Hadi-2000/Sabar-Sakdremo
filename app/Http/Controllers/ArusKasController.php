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
    public function indexCreate(){
        return view('tampilan.keuangan.kas-create');
    }
    public function indexUpdate($id){
        $arus = ArusKas::findOrFail($id); // Ambil data sesuai ID
            return view('tampilan.keuangan.kas-update', compact('arus'));
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
            $arus->where(function($q) use ($query, $isDate) {
                $q->where('keterangan', 'LIKE', '%'.$query.'%')
                  ->orWhere('created_at', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis_kas', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis_transaksi', 'LIKE', '%'.$query.'%');
        
                // Jika query berupa tanggal valid, tambahkan filter pada created_at
                if ($isDate && strtotime($query) !== false) {
                    $q->orWhereDate('created_at', '=', date('Y-m-d', strtotime($query)));
                }
            });
        }
    
        $arus = $arus->get(); // Ambil hasil query
    
        // Jika data kosong, redirect dengan pesan error
        if ($arus->isEmpty()) {
            return redirect()->route('keuangan.kas.index')->with('error', 'Data tidak ditemukan!');
        }
    
        return view('tampilan.keuangan.kas', compact('arus', 'query'));
    }
    public function create(Request $request) {
        // Validasi input
        $request->validate([
            'keterangan' => 'required|string',
            'jenis_kas' => 'required|string',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah_hidden' => 'required|numeric|min:1',
        ]);
        $jumlah = str_replace('.', '', $request->jumlah_hidden); // Hapus titik format rupiah
        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah tidak valid!');
        }

        // Cek apakah jenis kas ada di database
        $param = Kas::where('jenis_kas', $request->jenis_kas)->first();
        $param2 = Kas::where('jenis_kas', 'totalAsset')->first();
        if (!$param2) {
            return redirect()->back()->with('error', 'Data total asset tidak ditemukan!');
        }
        
        if (!$param) {
            return redirect()->back()->with('error', 'Jenis Kas tidak ditemukan!');
        }
    
        // Tentukan kategori kas
        $kas = ($param->jenis_kas == 'totalOnHand') ? "OnHand" : "Operasional";
        
        $idKas = $param->id;
        //cek jika idKas tidak ad
        if (!$idKas) {
            return redirect()->back()->with('error', 'Jenis Kas tidak ditemukan!');
        }
        // Hitung saldo baru
        $jumlahFix2 = ($request->jenis_transaksi == 'Masuk')
            ? $param->saldo + $jumlah
            : $param->saldo - $jumlah;

        $saldoAsset = $param2->saldo;

        $saldoAssetFix = ($request->jenis_transaksi == 'Masuk')
        ? $saldoAsset + $jumlah
        : $saldoAsset - $jumlah;
    
        // Pastikan saldo tidak negatif jika transaksi "Keluar"
        if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk transaksi keluar!');
        }
    
        // Update saldo di tabel Kas
        $param->update(['saldo' => $jumlahFix2]);
        $param2->update(['saldo' => $saldoAssetFix]);
    
        // Simpan transaksi ke ArusKas
        ArusKas::create([
            'idKas' => $param->id, // Pastikan nama kolom benar
            'keterangan' => $request->keterangan,
            'jenis_kas' => $kas,
            'jenis_transaksi' => $request->jenis_transaksi,
            'jumlah' => $jumlah,
        ]);
        return redirect()->route('keuangan.kas.index')->with('success', 'Tambah Data berhasil');
    }
    
    //hapus isi
    public function destroy($id){

    // Cari data kas berdasarkan idKas dari transaksi
    $param = ArusKas::find($id);
    

    $paramAsset = Kas::where('jenis_kas', 'totalAsset')->first();
    if (!$paramAsset) {
        return redirect()->back()->with('error', 'Data total asset tidak ditemukan!');
    }

    $paramOnHand = Kas::where('jenis_kas', 'totalOnHand')->first();
    if (!$paramOnHand) {
        return redirect()->back()->with('error', 'Data total onhand tidak ditemukan!');
    }

    $paramOperasional = Kas::where('jenis_kas', 'totalOperasional')->first();
    if (!$paramOperasional) {
        return redirect()->back()->with('error', 'Data total operasional tidak ditemukan!');
    }

    // Jika kas tidak ditemukan, tampilkan pesan error
    if (!$param) {
        return redirect()->back()->with('error', 'Data Transaksi tidak ditemukan!');
    }

    // Cari saldo awal kas
    if (!$paramOnHand && $param->jenis_kas == "OnHand") {
        return redirect()->back()->with('error', 'Data total onhand tidak ditemukan!');
    }
    
    if (!$paramOperasional && $param->jenis_kas == "Operasional") {
        return redirect()->back()->with('error', 'Data total operasional tidak ditemukan!');
    }

    if($param->jenis_kas == "OnHand"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $paramOnHand->saldo - $param->jumlah
        : $paramOnHand->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $paramAsset->saldo - $param->jumlah
        : $paramAsset->saldo + $param->jumlah;

         // Pastikan saldo tidak negatif setelah penghapusan
            if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk menghapus transaksi!');
            } else{
                // Update saldo di tabel Kas
                $paramOnHand->update(['saldo' => $jumlahFix2]);
                $paramAsset->update(['saldo' => $saldoAssetFix]);
            }
    } elseif($param->jenis_kas == "Operasional"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $paramOperasional->saldo - $param->jumlah
        : $paramOperasional->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $paramAsset->saldo - $param->jumlah
        : $paramAsset->saldo + $param->jumlah;

        // Pastikan saldo tidak negatif setelah penghapusan
        if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk menghapus transaksi!');
        } else{
            // Update saldo di tabel Kas
            $paramOperasional->update(['saldo' => $jumlahFix2]);
            $paramAsset->update(['saldo' => $saldoAssetFix]);
        }
    }

    // Hapus transaksi di ArusKas
    $param->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus!');
}

    // edit
    public function update(Request $request, $id){
        $arus = ArusKas::find($id);
        $kas_onhand = Kas::where('jenis_kas', 'totalOnHand')->first();
        $kas_asset = Kas::where('jenis_kas', 'totalAsset')->first();
        $kas_operasional = Kas::where('jenis_kas', 'totalOperasional')->first();

        $request->validate([
            'keterangan' => 'required|string',
            'jenis_kas' => 'required|string',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah_hidden' => 'required|numeric|min:1',
        ]);
        $jumlah = str_replace('.', '', $request->jumlah_hidden); // Hapus titik format rupiah
        if ($jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah tidak valid!');
        }
        $jumlah_sebelum = $arus->jumlah;
        //OnHand -> OnHand
        if($request->jenis_kas == $arus->jenis_kas && $arus->jenis_kas == 'OnHand') {
             //OnHand -> OnHand && masuk = masuk
            if($request->jenis_transaksi == $arus->transaksi && $arus->transaksi == 'Masuk') {
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_onhand->update(['saldo' => $kas_onhand->saldo - $jumlah_sebelum + $jumlah]);
                $kas_asset->update(['saldo' => $kas_asset->saldo - $jumlah_sebelum + $jumlah]);
                //mengurngi jumlah di arus kas menjadi 0 lalu menambkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnHand && masuk = keluar
            }elseif($request->jenis_transaksi != $arus->transaksi && $arus->transaksi == 'Masuk'){
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_onhand->update(['saldo' => $kas_onhand->saldo - $jumlah_sebelum - $jumlah]);
                $kas_asset->update(['saldo' => $kas_asset->saldo - $jumlah_sebelum - $jumlah]);
                //menambahkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnHand && keluar = keluar
            }elseif($request->jenis_transaksi == $arus->transaksi && $arus->transaksi == 'Keluar') {
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_onhand->update(['saldo' => $kas_onhand->saldo + $jumlah_sebelum - $jumlah]);
                $kas_asset->update(['saldo' => $kas_asset->saldo + $jumlah_sebelum - $jumlah]);
                //mengurngi jumlah di arus kas menjadi 0 lalu menambkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnHand && keluar = masuk
            }elseif($request->jenis_transaksi != $arus->transaksi && $arus->transaksi == 'Keluar'){
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_onhand->update(['saldo' => $kas_onhand->saldo + $jumlah_sebelum + $jumlah]);
                $kas_asset->update(['saldo' => $kas_asset->saldo + $jumlah_sebelum + $jumlah]);
                //menambahkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            }
        //OnHand -> Operasional
        }elseif($request->jenis_kas != $arus->jenis_kas && $arus->jenis_kas == 'OnHand') {
            //OnHand -> OnPerasional && masuk = masuk
            if($request->jenis_transaksi == $arus->transaksi && $arus->transaksi == 'Masuk') {
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_operasional->update(['saldo' => $kas_operasional->saldo + $jumlah]);
                $kas_onhand->update(['saldo' => $kas_onhand->saldo - $jumlah_sebelum]);
                $kas_asset->update(['saldo' => $kas_asset->saldo - $jumlah_sebelum + $jumlah]);
                //mengurngi jumlah di arus kas menjadi 0 lalu menambkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnPerasional && masuk = keluar
            }elseif($request->jenis_transaksi != $arus->transaksi && $arus->transaksi == 'Masuk'){
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_operasional->update(['saldo' => $kas_operasional->saldo - $jumlah]);
                $kas_onhand->update(['saldo' => $kas_onhand->saldo - $jumlah_sebelum]);
                $kas_asset->update(['saldo' => $kas_asset->saldo - $jumlah_sebelum - $jumlah]);
                //menambahkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnPerasional && keluar = keluar
            }elseif($request->jenis_transaksi == $arus->transaksi && $arus->transaksi == 'Keluar') {
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_operasional->update(['saldo' => $kas_operasional->saldo -  $jumlah]);
                $kas_onhand->update(['saldo' => $kas_onhand->saldo + $jumlah_sebelum]);
                $kas_asset->update(['saldo' => $kas_asset->saldo + $jumlah_sebelum - $jumlah]);
                //mengurngi jumlah di arus kas menjadi 0 lalu menambkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            //OnHand -> OnPerasional && keluar = masuk
            }elseif($request->jenis_transaksi != $arus->transaksi && $arus->transaksi == 'Keluar'){
                //sama menyesuaikan pada kas di jenis_kas dari totalAsset,dan totalOnHand
                $kas_operasional->update(['saldo' => $kas_operasional->saldo + $jumlah]);
                $kas_onhand->update(['saldo' => $kas_onhand->saldo + $jumlah_sebelum]);
                $kas_asset->update(['saldo' => $kas_asset->saldo + $jumlah_sebelum + $jumlah]);
                //menambahkan data yang dimasukan tadi
                $arus->update(['jumlah' => 0]);
                $arus->update(['keterangan' => $request->keterangan,'jenis_transaksi' => $request->jenis_transaksi, 'jumlah' => $jumlah]);
            }
        }

        return redirect()->route('keuangan.kas.index')->with('success', 'Data berhasil diubah!');
    }
}