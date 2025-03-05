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
        $arus = ArusKas::orderBy('created_at', 'desc')->paginate(10);
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
    
        $arus = $arus->orderBy('created_at', 'desc')->paginate(10); // Ambil hasil query
    
        // Jika data kosong, redirect dengan pesan error
        if ($arus->isEmpty()) {
            return redirect()->route('keuangan.kas.index')->with('error', 'Data tidak ditemukan!');
        }
    
        return view('tampilan.keuangan.kas', compact('arus', 'query'));
    }
    private const VALIDATION_RULE_STRING = 'required|string';
    public function create(Request $request) {
        // Validasi input
        $request->validate([
            'keterangan' => self::VALIDATION_RULE_STRING,
            'jenis_kas' => self::VALIDATION_RULE_STRING,
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'jumlah_hidden' => 'required|numeric|min:1',
        ]);
        $jumlah = str_replace('.', '', $request->jumlah_hidden); // Hapus titik format rupiah

        // Cek apakah jenis kas ada di database
        $param = Kas::where('jenis_kas', $request->jenis_kas)->first();
        $param2 = Kas::where('jenis_kas', 'totalAsset')->first();
        $kas = ($param->jenis_kas == 'OnHand') ? "OnHand" : "Operasional";

        $idKas = ($request->jenis_kas == 'OnHand') ? 2 : 10;
            
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
            'idKas' => $idKas, // Pastikan nama kolom benar
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
    $paramOnHand = Kas::where('jenis_kas', 'OnHand')->first();
    $paramOperasional = Kas::where('jenis_kas', 'Operasional')->first();

    // Jika kas tidak ditemukan, tampilkan pesan error
    if (!$param) {
        return redirect()->back()->with('error', 'Data Transaksi tidak ditemukan!');
    }

    if($param->jenis_kas == "OnHand"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $paramOnHand->saldo - $param->jumlah
        : $paramOnHand->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $paramAsset->saldo - $param->jumlah
        : $paramAsset->saldo + $param->jumlah;

    } elseif($param->jenis_kas == "Operasional"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $paramOperasional->saldo - $param->jumlah
        : $paramOperasional->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $paramAsset->saldo - $param->jumlah
        : $paramAsset->saldo + $param->jumlah;
    }
    if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
        return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk menghapus transaksi!');
    } else{
        // Update saldo di tabel Kas
        $paramOperasional->update(['saldo' => $jumlahFix2]);
        $paramAsset->update(['saldo' => $saldoAssetFix]);
    }

    // Hapus transaksi di ArusKas
    $param->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus!');
}

    // edit
    public function update(Request $request, $id)
{
    $arus = ArusKas::find($id);
    $kas_onhand = Kas::where('jenis_kas', 'OnHand')->first();
    $kas_asset = Kas::where('jenis_kas', 'totalAsset')->first();
    $kas_operasional = Kas::where('jenis_kas', 'Operasional')->first();

    // Validasi input
    $request->validate([
        'keterangan' => self::VALIDATION_RULE_STRING,
        'jenis_kas' => self::VALIDATION_RULE_STRING,
        'jenis_transaksi' => self::VALIDATION_RULE_STRING,
        'jumlah_hidden' => 'numeric|min:1',
    ]);

    // Format jumlah dari input
    $jumlah = str_replace('.', '', $request->jumlah_hidden);
    if ($jumlah <= 0) {
        return redirect()->back()->with('error', 'Jumlah tidak valid!');
    }

    $jumlah_sebelum = $arus->jumlah;
    $jenis_kas_lama = $arus->jenis_kas;
    $jenis_kas_baru = $request->jenis_kas;
    $transaksi_lama = $arus->jenis_transaksi;
    $transaksi_baru = $request->jenis_transaksi;

    // Hitung faktor perubahan saldo
    $factor = ($transaksi_baru == $transaksi_lama) ? 1 : -1;
    $factor = ($transaksi_lama == 'Masuk') ? -$factor : $factor;

    // Jika jenis kas berubah, saldo jenis kas sebelumnya juga ikut diperbarui
    if ($jenis_kas_lama !== $jenis_kas_baru) {
        $kas_lama = Kas::where('jenis_kas', $jenis_kas_lama)->first();
        $kas_baru = Kas::where('jenis_kas', $jenis_kas_baru)->first();

        if ($kas_lama) {
            $kas_lama->update(['saldo' => $kas_lama->saldo - ($factor * $jumlah_sebelum)]);
        }
        if ($kas_baru) {
            $kas_baru->update(['saldo' => $kas_baru->saldo + ($factor * $jumlah)]);
        }
    } else {
        // Jika jenis kas tetap sama, hanya update saldo di kas target
        if ($jenis_kas_lama == 'OnHand') {
            $this->updateSaldo($kas_onhand, $kas_asset, $jumlah_sebelum, $jumlah, $factor);
        } elseif ($jenis_kas_lama == 'Operasional') {
            $this->updateSaldo($kas_operasional, $kas_asset, $jumlah_sebelum, $jumlah, $factor);
        }
    }

    $idKas = ($request->jenis_kas == 'OnHand') ? 2 : 10;

    // Update data transaksi
    $arus->update([
        'idKas' => $idKas, // Pastikan nama kolom benar
        'keterangan' => $request->keterangan,
        'jenis_kas' => $request->jenis_kas,
        'jenis_transaksi' => $transaksi_baru,
        'jumlah' => $jumlah
    ]);

    return redirect()->route('keuangan.kas.index')->with('success', 'Data berhasil diubah!');
}
    
    /**
     * Fungsi untuk memperbarui saldo secara otomatis
     */
    private function updateSaldo($kas_target, $kas_asset, $jumlah_sebelum, $jumlah, $factor)
    {
        $kas_target->update(['saldo' => $kas_target->saldo + $factor * ($jumlah_sebelum + $jumlah)]);
        $kas_asset->update(['saldo' => $kas_asset->saldo + $factor * ($jumlah_sebelum + $jumlah)]);
    }
    
    }
    
