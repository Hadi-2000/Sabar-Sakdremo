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
use App\Models\Pegawai;

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
        try {
            $arus = ArusKas::latest('created_at')->paginate(10);
    
            $kasData = Kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'
            ])->get()->keyBy('jenis_kas');
             //$this->kas();
            return view('tampilan.keuangan.kas', compact('arus'));
    
        } catch (\Exception $e) {
            Log::error('Error pada ArusKasController@index: '. $e->getMessage());
            return redirect()->route('kas.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    
    public function create(){
        return view('tampilan.keuangan.kas-create');
    }
    public function edit($id) { 
        $arus = ArusKas::find($id);
        $arus->jumlah = number_format($arus->jumlah, 0, ',', '.');
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
             $tgl =  date('Y-m-d');
          
            $jumlah = str_replace('.', '', $data['jumlah_hidden']); 
             $jumlah2 = ($data['jenis_transaksi'] == 'Masuk')
            ?  $jumlah
            : -$jumlah;
    
            // Cek apakah jenis kas ada di database
            $kasData = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'])->get()->keyBy('jenis_kas');
                
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

             $jumlah = ($data['jenis_transaksi'] == 'Masuk')
            ?  $jumlah
            : -$jumlah;
        
            // Pastikan saldo tidak negatif jika transaksi "Keluar"
            if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
                return back()->with('error', 'Saldo tidak mencukupi untuk transaksi keluar!');
            }
        
            // Update saldo di tabel Kas
            $kasData[$data['jenis_kas']]->update(['saldo' => $jumlahFix2]);
        
            // Simpan transaksi ke ArusKas
            ArusKas::create([
                'idKas' =>  $kasData[$data['jenis_kas']]->id,
                'keterangan' => $data['keterangan'],
                'jenis_kas' => $data['jenis_kas'],
                'jenis_transaksi' => $data['jenis_transaksi'],
                'jumlah' => $jumlah2,
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

            if(!$param){
                return redirect()->back()->with('error','Data tidak ditemukan');
            }
            // Cari data kas berdasarkan idKas dari transaksi
           $kasData = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'])->get()->keyBy('jenis_kas');
        
            
            $jumlahFix2 = ($param->jenis_transaksi == 'Masuk')
            ? $kasData[$param->jenis_kas]->saldo - $param->jumlah
            : $kasData[$param->jenis_kas]->saldo + $param->jumlah;
        
            $saldoAssetFix = ($param->jenis_transaksi == 'Masuk')
            ? $kasData['totalAsset']->saldo - $param->jumlah
            : $kasData['totalAsset']->saldo + $param->jumlah;
        
            if ($jumlahFix2 < 0 || $saldoAssetFix < 0) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk menghapus transaksi!');
            } else{
                // Update saldo di tabel Kas
                $kasData['Operasional']->update(['saldo' => $jumlahFix2]);
                $totalAsset = $kasData['OnHand']->saldo + 
                $kasData['Operasional']->saldo + 
               $kasData['Piutang']->saldo + 
                $kasData['Stock']->saldo - 
                $kasData['Utang']->saldo;
            
                if ($kasData['totalAsset']->saldo != $totalAsset) {
                    $kasData['totalAsset']->update(['saldo' => $totalAsset]);
                }
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
        if(!$arus){
            return redirect()->back()->with('error','Data tidak ditemukan');
        }
        $kasData = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'])->get()->keyBy('jenis_kas');
        
        // Validasi input
        $data = $request->validated();
    
        // Format jumlah dari input
        $jumlah = str_replace('.', '', $data['jumlah_hidden']);
        $jumlah2 = ($data['jenis_transaksi'] == 'Masuk') ? $jumlah : -$jumlah;
        if ($jumlah <= 0) {
            return back()->with('error', 'Jumlah tidak valid!');
        }
    
        // Hitung faktor perubahan saldo
        $factor = ($data['jenis_transaksi'] == $arus->jenis_transaksi) ? 1 : -1;
        $factor = ($arus->jenis_transaksi == 'Masuk') ? -$factor : $factor;
    
        // Jika jenis kas berubah, saldo jenis kas sebelumnya juga ikut diperbarui
        if ($arus->jenis_kas !== $data['jenis_kas']) {
    
            if ($kasData[$arus->jenis_kas]) {
                $kasData[$arus->jenis_kas]->update(['saldo' => $kasData[$arus->jenis_kas]->saldo - ($factor * $arus->jumlah)]);
            }
            if ($kasData[$data['jenis_kas']]) {
                $kasData[$data['jenis_kas']]->update(['saldo' => $kasData[$data['jenis_kas']]->saldo + ($factor * $jumlah)]);
            }
        } else {
            // Jika jenis kas tetap sama, hanya update saldo di kas target
            if ($arus->jenis_kas == 'OnHand') {
                $this->updateSaldo($kasData['OnHand'], $kasData['totalAsset'], $arus->jumlah, $jumlah, $factor);
            } elseif ($arus->jenis_kas == 'Operasional') {
                $this->updateSaldo($kasData['Operasional'], $kasData['totalAsset'], $arus->jumlah, $jumlah, $factor);
            }
        }
    
        // Update data transaksi
        $arus->update([
            'idKas' =>$kasData[$data['jenis_kas']]->id, // Pastikan nama kolom benar
            'keterangan' => $data['keterangan'],
            'jenis_kas' => $data['jenis_kas'],
            'jenis_transaksi' => $data['jenis_transaksi'],
            'jumlah' => $jumlah2
        ]);

         $totalAsset = $kasData['OnHand']->saldo + 
                $kasData['Operasional']->saldo + 
               $kasData['Piutang']->saldo + 
                $kasData['Stock']->saldo - 
                $kasData['Utang']->saldo;
            
        if ($kasData['totalAsset']->saldo != $totalAsset) {
            $kasData['totalAsset']->update(['saldo' => $totalAsset]);
        }
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
            $kas_target->update(['saldo' => $kas_target->saldo - ($factor * $jumlah_sebelum) + ($factor * $jumlah)]);
            $kas_asset->update(['saldo' => $kas_asset->saldo - ($factor * $jumlah_sebelum) + ($factor * $jumlah)]);
        });
    }
    
    private function kas(){
         $tgl =  date('Y-m-d');
         $kasData = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'])->get()->keyBy('jenis_kas');
         // Update saldo di tabel Kas totalAsset
            $totalAsset = $kasData['OnHand']->saldo + 
                $kasData['Operasional']->saldo + 
               $kasData['Piutang']->saldo + 
                $kasData['Stock']->saldo - 
                $kasData['Utang']->saldo;
            
            if ($kasData['totalAsset']->saldo != $totalAsset) {
                $kasData['totalAsset']->update(['saldo' => $totalAsset]);
            }
            //update pemasukan,pengeluaran
            $pemasukan = ArusKas::where('updated_at', $tgl)->where('jenis_transaksi', 'Masuk')->sum('jumlah');
            if($kasData['pemasukan']->saldo != $pemasukan){
                $kasData['pemasukan']->update(['saldo' => $pemasukan]);
            }
             $pengeluaran = ArusKas::where('updated_at', $tgl)->where('jenis_transaksi', 'Keluar')->sum('jumlah');
            if($kasData['pengeluaran']->saldo != $pengeluaran){
                $kasData['pengeluaran']->update(['saldo' => $pengeluaran]);
            }
            $labaKotor = $pemasukan + $pengeluaran;
            if($kasData['labaKotor']->saldo != $labaKotor){
                $kasData['labaKotor']->update(['saldo' => $labaKotor]);
            }
            $pegawai = Pegawai::where('kehadiran','Pulang')->where('updated_at',$tgl)->sum('gaji_hari_ini');
            if($kasData['labaBersih']->saldo != $labaKotor - $pegawai){
                $kasData['labaBersih']->update(['saldo' => $labaKotor - $pegawai]);
            }
            $selisih = ArusKas::where('keterangan','gap')->sum('jumlah');
            if($kasData['selisih']->saldo != $selisih){
                $kasData['selisih']->update(['saldo' => $selisih]);
            }
    }
    
    }