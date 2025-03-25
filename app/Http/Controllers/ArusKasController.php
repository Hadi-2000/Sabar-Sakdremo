<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\ArusKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdatekasRequest;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreArus_KasRequest;
use App\Http\Requests\UpdateArus_KasRequest;

class ArusKasController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    public function index(){
        try{
            $arus = ArusKas::latest('created_at')->paginate(10);
            return view('tampilan.keuangan.kas', compact('arus'));
        }catch(\Exception $e){
            Log::error('Error pada ArusKasController@index'. $e->getMessage());
            return redirect()->route('keuangan.kas.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
        
    }
    public function create(){
        return view('tampilan.keuangan.kas-create');
    }
    public function edit($id) { 
        $arus = ArusKas::find($id);
        return view('tampilan.keuangan.kas-update', compact('arus'));
    }
    public function search(Request $request){
        try{
            if (session()->has('error')) {
                session()->forget('error');
            }
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));
            // Cek apakah query hanya berupa angka (kemungkinan pencarian tanggal)
            $isDate = strtotime($query) !== false;
        
        
            if (!empty($query)) {
                $arus = ArusKas::where(function($q) use ($query, $isDate) {
                    $q->where('keterangan', 'LIKE', "%{$query}%")
                      ->orWhere('created_at', 'LIKE', "%{$query}%")
                      ->orWhere('jenis_kas', 'LIKE', "%{$query}%")
                      ->orWhere('jenis_transaksi', 'LIKE', "%{$query}%");
            
                    // Jika query berupa tanggal valid, tambahkan filter pada created_at
                    if ($isDate && strtotime($query) !== false) {
                        $q->orWhereDate('created_at', '=', date('Y-m-d', strtotime($query)));
                    }
                })
                ->latest('created_at')
                ->paginate(10);
            }else{
                $arus = ArusKas::latest('created_at')->paginate(10);
            }
        
            // Jika data kosong, redirect dengan pesan error
            if ($arus->isEmpty()) {
                return redirect()->route('kas.index')->with('error', 'Data tidak ditemukan!');
            }
        
            return view('tampilan.keuangan.kas', compact('arus', 'query'));
        }catch(\Exception $e){
            Log::error('Error pada ArusKasController@search'. $e->getMessage());
            return redirect()->route('kas.index')->with('error', 'Terjadi kesalahan saat mencari data.');
        }
    }

    public function store(StoreArus_KasRequest $request) {
        try{
            DB::beginTransaction();
            // Validasi input
            $data = $request->validated();
    
            $jumlah = str_replace('.', '', $data['jumlah_hidden']); // Hapus titik format rupiah
    
            // Cek apakah jenis kas ada di database
            $kasData = Kas::whereIn('jenis_kas', [$data['jenis_kas'], 'totalAsset'])->get()->keyBy('jenis_kas');
            $kas = ($kasData[$data['jenis_kas']] == 'OnHand') ? "OnHand" : "Operasional";
            $idKas = ($data['jenis_kas'] == 'OnHand') ? 2 : 10;
                
            // Hitung saldo baru
            if(isset($kasData[$data['jenis_kas']])){
                $jumlahFix2 = ($data['jenis_transaksi'] == 'Masuk')
                ? $kasData[$data['jenis_kas']]->saldo + $jumlah
                : $kasData[$data['jenis_kas']]->saldo - $jumlah;
            }else {
                return back()->with('error', 'Jenis kas tidak ditemukan!');
            }
    
            $saldoAssetFix = ($data['jenis_transaksi'] == 'Masuk')
            ? $kasData['totalAsset']->saldo + $jumlah
            : $kasData['totalAsset']->saldo - $jumlah;
        
            // Pastikan saldo tidak negatif jika transaksi "Keluar"
            if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
                return back()->with('error', 'Saldo tidak mencukupi untuk transaksi keluar!');
            }
        
            // Update saldo di tabel Kas
            $kasData[$data['jenis_kas']]->update(['saldo' => $jumlahFix2]);
            $kasData['totalAsset']->update(['saldo' => $saldoAssetFix]);
        
            // Simpan transaksi ke ArusKas
            ArusKas::create([
                'idKas' => $idKas, // Pastikan nama kolom benar
                'keterangan' => $data['keterangan'],
                'jenis_kas' => $kas,
                'jenis_transaksi' => $data['jenis_transaksi'],
                'jumlah' => $jumlah,
            ]);
            DB::commit();
            return redirect()->route('kas.index')->with('success', 'Tambah Data berhasil');
        }catch(\Exception $e){
            Log::error('Error pada ArusKasController@store'. $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }catch(\PDOException $e){
            Log::error('Error pada ArusKasController@store'. $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan koneksi ke database.');
        }
    }
    
    //hapus isi
    public function destroy($id){
        try{
            DB::beginTransaction();
            $param = ArusKas::find($id);
            // Cari data kas berdasarkan idKas dari transaksi
            $kasData = Kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional'])->get()->keyBy('jenis_kas');
        
            if($param->jenis_kas == "OnHand"){
                // Hitung saldo baru
                $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
                ? $kasData['OnHand']->saldo - $param->jumlah
                : $kasData['OnHand']->saldo + $param->jumlah;
        
                $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
                ? $kasData['totalAsset']->saldo - $param->jumlah
                : $kasData['totalAsset']->saldo + $param->jumlah;
        
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
            DB::commit();
        
            return redirect()->back()->with('success', 'Data berhasil dihapus!');
        }catch(\Exception $e){
            Log::error('Error pada ArusKasController@destroy'. $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }catch(\PDOException $e){
            Log::error('Error pada ArusKasController@destroy'. $e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan koneksi ke database.');
        }
}

    // edit
    public function update(StoreArus_KasRequest $request, $id)
{
    try{
        DB::beginTransaction();
        // Cari data transaksi berdasarkan id
        $arus = ArusKas::find($id);
        $kasData = Kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional'])->get()->keyBy('jenis_kas');
    
        // Validasi input
        $data = $request->validated();
    
        // Format jumlah dari input
        $jumlah = str_replace('.', '', $data['jumlah_hidden']);
        if ($jumlah <= 0) {
            return back()->with('error', 'Jumlah tidak valid!');
        }
        [$jumlah_sebelum, $jenis_kas_lama, $jenis_kas_baru, $transaksi_lama, $transaksi_baru] =
        tap($arus, fn($a) => [$a->jumlah, $a->jenis_kas, $data['jenis_kas'], $a->jenis_transaksi, $data['jenis_transaksi']]);
    
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
    
        $idKas = ($data['jenis_kas'] == 'OnHand') ? 2 : 10;
    
        // Update data transaksi
        $arus->update([
            'idKas' => $idKas, // Pastikan nama kolom benar
            'keterangan' => $data['keterangan'],
            'jenis_kas' => $data['jenis_kas'],
            'jenis_transaksi' => $data['jenis_transaksi'],
            'jumlah' => $jumlah
        ]);
        DB::commit();
    
        return redirect()->route('kas.index')->with('success', 'Data berhasil diubah!');
    }catch(\Exception $e){
        Log::error('Error pada ArusKasController@update'. $e->getMessage());
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah data.');
    }catch(\PDOException $e){
        Log::error('Error pada ArusKasController@update'. $e->getMessage());
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan koneksi ke database.');
    }
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