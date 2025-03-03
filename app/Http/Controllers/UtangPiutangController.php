<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\ArusKas;
use App\Models\Pelanggan;
use App\Models\UtangPiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facedes\view;
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
    public function create(Request $request) {
        $request->validate([
            'nama_pelanggan' => self::VALIDATION_RULE_STRING,
            'alamat_pelanggan' => self::VALIDATION_RULE_STRING,
            'no_telepon' => 'nullable|numeric',
            'jumlah_hidden' => 'required|numeric|min:1',
            'jenis' => self::VALIDATION_RULE_STRING,
            'status' => self::VALIDATION_RULE_STRING,
            'keterangan' => 'nullable|string'
        ]);
    
        // Format angka (menghilangkan titik pemisah ribuan)
        $jumlah = (int) str_replace('.', '', $request->jumlah_hidden);
    
        // Mengambil data kas berdasarkan jenisnya
        $utang = Kas::where('jenis_kas', 'Utang')->first();
        $piutang = Kas::where('jenis_kas', 'Piutang')->first();
        $operasional = Kas::where('jenis_kas', 'Operasional')->first();
        $totalAsset = Kas::where('jenis_kas', 'totalAsset')->first();
    
        // Validasi apakah kas ditemukan
        if (!$utang || !$piutang || !$operasional || !$totalAsset) {
            return back()->with('error', 'Kas tidak ditemukan.');
        }
    
        // Mencari atau membuat pelanggan baru
        $pelanggan = Pelanggan::firstOrCreate(
            ['nama' => $request->nama_pelanggan],
            ['alamat' => $request->alamat_pelanggan, 'no_telepon' => '']
        );
    
        // Hitung saldo kas baru
        $utang_fix = $utang->saldo + $jumlah;
        $totalAsset_fix = $totalAsset->saldo - $jumlah;
    
        // Simpan transaksi utang/piutang
        UtangPiutang::create([
            'id_pelanggan' => $pelanggan->id,
            'nama' => $request->nama_pelanggan,
            'alamat' => $request->alamat_pelanggan,
            'keterangan' => $request->keterangan ?? '-',
            'jenis' => $request->jenis,
            'nominal' => $jumlah,
            'status' => $request->status
        ]);
    
        // Update saldo kas & total asset
        if ($request->jenis == 'Utang') {
            $utang->update(['saldo' => $utang_fix]);
            $totalAsset->update(['saldo' => $totalAsset_fix]);
        } else {
            $piutang->update(['saldo' => $utang_fix]);
        }
    
        // Update pelanggan dan kas operasional sesuai jenis transaksi
        if (empty($pelanggan->utangPiutang) || $pelanggan->utangPiutang == 'Utang') {
            $jenis_transaksi = 'Keluar';
            $pelanggan->update(['total' => $utang_fix, 'utangPiutang' => 'Utang']);
            $operasional->update(['saldo' => $operasional->saldo - $utang_fix]);
            $utang->update(['saldo' => $utang->saldo + $utang_fix]);
            $totalAsset->update(['saldo' => $totalAsset_fix - $utang_fix]);
        } elseif ($pelanggan->utangPiutang == 'Piutang') {
            if ($jumlah > $pelanggan->total) {
                $jenis_transaksi = 'Masuk';
                $utang_sebelum = $pelanggan->total;
                $piutang_fix = $jumlah - $utang_sebelum;
                $totalAsset->update(['saldo' => $totalAsset_fix - $utang->saldo + $piutang_fix]);
                $pelanggan->update(['total' => $piutang_fix, 'utangPiutang' => 'Piutang']);
                $operasional->update(['saldo' => $operasional->saldo - $piutang_fix]);
                $utang->update(['saldo' => 0]);
                $piutang->update(['saldo' => $piutang_fix]);
            } else {
                $jenis_transaksi = 'Keluar';
                $utang_fix2 = $pelanggan->total - $jumlah;
                $totalAsset->update(['saldo' => $totalAsset_fix - $jumlah]);
                $pelanggan->update(['total' => $utang_fix2, 'utangPiutang' => 'Piutang']);
                $operasional->update(['saldo' => $operasional->saldo + $utang_fix]);
                $utang->update(['saldo' => $utang->saldo + $utang_fix]);
                $piutang->update(['saldo' => $piutang->saldo - $utang_fix]);
            }
        }
    
        // Simpan transaksi arus kas
        ArusKas::create([
            'idKas' => 10, // Pastikan ID benar
            'keterangan' => $request->keterangan ?? '-',
            'jenis_kas' => 'Operasional',
            'jenis_transaksi' => $jenis_transaksi,
            'jumlah' => $jumlah
        ]);
    
        return view('keuangan.utang');
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