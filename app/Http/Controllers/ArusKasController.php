<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreArus_KasRequest;
use App\Http\Requests\UpdateArus_KasRequest;
use App\Http\Requests\UpdatekasRequest;

class ArusKasController extends Controller
{
    public function index(){
        $arus = ArusKas::latest('created_at')->paginate(10);
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
        
        $query = strtolower($request->input('query'));
        
        // Cek apakah query hanya berupa angka (kemungkinan pencarian tanggal)
        $isDate = strtotime($query) !== false;
    
    
        if (!empty($query)) {
            $arus = ArusKas::where(function($q) use ($query, $isDate) {
                $q->where('keterangan', 'LIKE', '%'.$query.'%')
                  ->orWhere('created_at', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis_kas', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis_transaksi', 'LIKE', '%'.$query.'%');
        
                // Jika query berupa tanggal valid, tambahkan filter pada created_at
                if ($isDate && strtotime($query) !== false) {
                    $q->orWhereDate('created_at', '=', date('Y-m-d', strtotime($query)));
                }
            })
            ->latest('created_at')
            ->paginate(10);
        }
    
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
        $kasData = Kas::whereIn('jenis_kas', [$request->jenis_kas, 'totalAsset'])->get()->keyBy('jenis_kas');
        $kas = ($kasData[$request->jenis_kas]->jenis_kas == 'OnHand') ? "OnHand" : "Operasional";
        $idKas = ($request->jenis_kas == 'OnHand') ? 2 : 10;
            
        // Hitung saldo baru
        if(isset($kasData[$request->jenis_kas])){
            $jumlahFix2 = ($request->jenis_transaksi == 'Masuk')
            ? $kasData[$request->jenis_kas]->saldo + $jumlah
            : $kasData[$request->jenis_kas]->saldo - $jumlah;
        }else {
            return redirect()->back()->with('error', 'Jenis kas tidak ditemukan!');
        }

        $saldoAssetFix = ($request->jenis_transaksi == 'Masuk')
        ? $kasData['totalAsset']->saldo + $jumlah
        : $kasData['totalAsset']->saldo - $jumlah;
    
        // Pastikan saldo tidak negatif jika transaksi "Keluar"
        if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk transaksi keluar!');
        }
    
        // Update saldo di tabel Kas
        $kasData[$request->jenis_kas]->update(['saldo' => $jumlahFix2]);
        $kasData['totalAsset']->update(['saldo' => $saldoAssetFix]);
    
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
    $param = ArusKas::findOrFail($id);
    $kasData = Kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional'])->get()->keyBy('jenis_kas');

    if($param->jenis_kas == "OnHand"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $kasData['OnHand']->saldo - $param->jumlah
        : $kasData['OnHand']->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $kasData['totalAsset']->saldo - $param->jumlah
        : $kasData['totalAsset'] + $param->jumlah;

    } elseif($param->jenis_kas == "Operasional"){
        // Hitung saldo baru
        $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
        ? $kasData['Operasional']->saldo - $param->jumlah
        : $kasData['Operasional']->saldo + $param->jumlah;

        $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
        ? $kasData['totalAsset']->saldo - $param->jumlah
        : $kasData['totalAsset']->saldo + $param->jumlah;
    }
    if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
        return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk menghapus transaksi!');
    } else{
        // Update saldo di tabel Kas
        $kasData['Operasional']->update(['saldo' => $jumlahFix2]);
        $kasData['totalAsset']->update(['saldo' => $saldoAssetFix]);
    }

    // Hapus transaksi di ArusKas
    $param->delete();

    return redirect()->back()->with('success', 'Data berhasil dihapus!');
}

    // edit
    public function update(Request $request, $id)
{
    $arus = ArusKas::findOrFail($id);
    $kasData = Kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional'])->get()->keyBy('jenis_kas');

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
    [$jumlah_sebelum, $jenis_kas_lama, $jenis_kas_baru, $transaksi_lama, $transaksi_baru] =
    tap($arus, fn($a) => [$a->jumlah, $a->jenis_kas, $request->jenis_kas, $a->jenis_transaksi, $request->jenis_transaksi]);

    // Hitung faktor perubahan saldo
    $factor = ($transaksi_baru == $transaksi_lama) ? 1 : -1;
    $factor = ($transaksi_lama == 'Masuk') ? -$factor : $factor;

    // Jika jenis kas berubah, saldo jenis kas sebelumnya juga ikut diperbarui
    if ($jenis_kas_lama !== $jenis_kas_baru) {
        $kas_jumlah = Kas::whereIn('jenis_kas',[$jenis_kas_lama,$jenis_kas_baru])->get()->keyBy('jenis_kas');

        if ($kas_jumlah[$jenis_kas_lama]) {
            $kas_jumlah[$jenis_kas_lama]->update(['saldo' => $kas_jumlah[$jenis_kas_lama]->saldo - ($factor * $jumlah_sebelum)]);
        }
        if ($kas_jumlah[$jenis_kas_baru]) {
            $kas_jumlah[$jenis_kas_baru]->update(['saldo' => $kas_jumlah[$jenis_kas_baru]->saldo + ($factor * $jumlah)]);
        }
    } else {
        // Jika jenis kas tetap sama, hanya update saldo di kas target
        if ($jenis_kas_lama == 'OnHand') {
            $this->updateSaldo($kasData['OnHand'], $kasData['totalAsset'], $jumlah_sebelum, $jumlah, $factor);
        } elseif ($jenis_kas_lama == 'Operasional') {
            $this->updateSaldo($kasData['Operasional'], $kasData['totalAsset'], $jumlah_sebelum, $jumlah, $factor);
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
        DB::transaction(function () use ($kas_target, $kas_asset, $jumlah_sebelum, $jumlah, $factor){
            $kas_target->update(['saldo' => $kas_target->saldo + $factor * ($jumlah_sebelum + $jumlah)]);
            $kas_asset->update(['saldo' => $kas_asset->saldo + $factor * ($jumlah_sebelum + $jumlah)]);
        });
    }
    
    }
    
