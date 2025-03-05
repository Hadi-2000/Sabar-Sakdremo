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
        $utang = UtangPiutang::orderBy('nama', 'desc')->paginate(10);
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
    
            $utang = $utang->orderBy('nama', 'desc')->paginate(10);
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
    public function createUtang(Request $request) {
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'alamat_pelanggan' => 'required|string',
            'jumlah_hidden' => 'required|numeric|min:1',
            'jenis' => 'required|string',
            'keterangan' => 'required|string',
            'status' => 'required|string',
            'ambil' => 'required|string'
        ]);
        $jumlah = (int) str_replace('.', '', $request->jumlah_hidden);
        
        $kas = $this->getKas();
        if (!$kas) return back()->with('error', 'Kas tidak ditemukan.');
    
        $pelanggan = $this->getPelanggan($request);
        $jenis_transaksi = '';
    
        // Hitung saldo utang/piutang
        if ($request->jenis === 'Utang') {
            $this->prosesUtang($pelanggan, $jumlah, $kas, $jenis_transaksi);
        } else {
            $this->prosesPiutang($pelanggan, $jumlah, $kas, $jenis_transaksi);
        }
        
        // Update totalAsset dan sumber pengambilan kas
        $this->updateTotalAsset($kas['totalAsset'], $jumlah, $request->jenis);
        $this->updateSumberKas($kas, $jumlah, $request->ambil);
        
        // Simpan transaksi arus kas
        ArusKas::create([
            'idKas' => 10,
            'keterangan' => $request->keterangan ?? '-',
            'jenis_kas' => $request->ambil,
            'jenis_transaksi' => $jenis_transaksi,
            'jumlah' => $jumlah
        ]);
        return redirect()->route('keuangan.utang.index')->with('success', 'Data Utang Berhasil Ditambahkan.');
    }
    
    private function getKas() {
        return [
            'utang' => Kas::where('jenis_kas', 'Utang')->first(),
            'piutang' => Kas::where('jenis_kas', 'Piutang')->first(),
            'operasional' => Kas::where('jenis_kas', 'Operasional')->first(),
            'totalAsset' => Kas::where('jenis_kas', 'totalAsset')->first(),
            'stok' => Kas::where('jenis_kas', 'Stok')->first(),
            'kasOnHand' => Kas::where('jenis_kas', 'KasOnHand')->first(),
        ];
    }
    
    private function getPelanggan($request) {
        return Pelanggan::firstOrCreate(
            ['nama' => $request->nama_pelanggan],
            ['alamat' => $request->alamat_pelanggan, 'no_telepon' => $request->no_telepon ?? '']
        );
    }
    
    private function prosesUtang($pelanggan, $jumlah, $kas, &$jenis_transaksi) {
        $utang_fix = $kas['utang']->saldo + $jumlah;
        $kas['utang']->update(['saldo' => $utang_fix]);
        $pelanggan->update(['total' => $utang_fix, 'utangPiutang' => 'Utang']);
        $jenis_transaksi = 'Keluar';
    }
    
    private function prosesPiutang($pelanggan, $jumlah, $kas, &$jenis_transaksi) {
        if ($jumlah > $pelanggan->total) {
            $piutang_fix = $jumlah - $pelanggan->total;
            $kas['piutang']->update(['saldo' => $piutang_fix]);
            $pelanggan->update(['total' => $piutang_fix, 'utangPiutang' => 'Piutang']);
            $jenis_transaksi = 'Masuk';
        } else {
            $utang_fix = $pelanggan->total - $jumlah;
            $kas['utang']->update(['saldo' => $utang_fix]);
            $pelanggan->update(['total' => $utang_fix, 'utangPiutang' => 'Piutang']);
            $jenis_transaksi = 'Keluar';
        }
    }
    
    private function updateTotalAsset($totalAsset, $jumlah, $jenis) {
        if ($jenis === 'Utang') {
            $totalAsset->update(['saldo' => $totalAsset->saldo - $jumlah]);
        } else {
            $totalAsset->update(['saldo' => $totalAsset->saldo + $jumlah]);
        }
    }
    
    private function updateSumberKas($kas, $jumlah, $ambil) {
        if (isset($kas[$ambil])) {
            $kas[$ambil]->update(['saldo' => $kas[$ambil]->saldo - $jumlah]);
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