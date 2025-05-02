<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\kas;
use App\Models\Aset;
use App\Models\Stock;
use App\Models\ArusKas;
use App\Models\pelanggan;
use App\Models\UtangPiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class PiutangController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    public function index()
    {
        try{
            $utang = UtangPiutang::where('jenis','Piutang')->orderBy('nama')->paginate(10);
            return view('tampilan.keuangan.piutang.index', compact('utang'));
        }catch(\Exception $e){
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('keuangan.piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }
    }

    public function search(Request $request){
        try{
            if(session()->has('error')){
                session()->forget('error');
            }
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));
            // Implement your search logic here
            if(empty($query)){
                $utang = UtangPiutang::where('jenis','Piutang')->orderBy('nama')->paginate(10);
            }else{
                $utang = UtangPiutang::where('nama','LIKE',"%{$query}%")
                                ->orWhere('keterangan','LIKE',"%{$query}%")
                                ->orWhere('alamat', 'LIKE', "%{$query}%")
                                ->orWhere('nominal', 'LIKE', "%{$query}%")
                                ->orWhere('status', 'LIKE', "%{$query}%")
                                ->where('jenis','Piutang')
                                ->orderBy('nama')
                                ->paginate(10);
            }
    
            return view('tampilan.keuangan.piutang.index', compact('utang','query'));
        }catch(\Exception $e){
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('keuangan.piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stock = Stock::all();
        return view('tampilan.keuangan.piutang.create',compact('stock'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUtangPiutangRequest $request)
    {
        try {
            DB::beginTransaction();
    
            $data = $request->validated();
            $data['jumlah'] = str_replace('.', '', $data['jumlah']);
            $jumlah = (int) $data['jumlah'];
    
            // Cek atau buat pelanggan berdasarkan nama
            $param = pelanggan::firstOrCreate(
                ['nama' => $data['nama_pelanggan']],
                ['alamat' => $data['alamat_pelanggan']]
            );
    
            // Perbarui alamat jika sudah ada tapi berbeda
            if ($param->wasRecentlyCreated === false && $param->alamat !== $data['alamat_pelanggan']) {
                $param->update(['alamat' => $data['alamat_pelanggan']]);
            }
    
            // Simpan data utang/piutang
            UtangPiutang::create([
                'id_pelanggan'   => $param->id,
                'nama'           => $data['nama_pelanggan'],
                'alamat'         => $data['alamat_pelanggan'],
                'keterangan'     => $data['keterangan'],
                'jenis'          => 'Piutang',
                'nominal'        => $jumlah,
                'status'         => 'Belum Lunas',
            ]);
    
            // Ambil semua jenis kas yang dibutuhkan
            $kas = kas::whereIn('jenis_kas', ['totalAsset', 'OnHand', 'Operasional', 'Utang', 'Piutang', 'stock'])->get();
    
            // Ambil masing-masing jenis kas
            $kasPiutang     = $kas->where('jenis_kas', 'Piutang')->first();
            $kasUtang       = $kas->where('jenis_kas', 'Utang')->first();
            $kasOnHand      = $kas->where('jenis_kas', 'OnHand')->first();
            $kasOperasional = $kas->where('jenis_kas', 'Operasional')->first();
            $kasStock       = $kas->where('jenis_kas', 'stock')->first();
            $kasTotalAsset  = $kas->where('jenis_kas', 'totalAsset')->first();
    
            // Tambahkan ke saldo piutang
            if ($kasPiutang) {
                $kasPiutang->update(['saldo' => $kasPiutang->saldo + $jumlah]);
            }
    
            // Tambahkan piutang ke pelanggan
            $param->update([
                'piutang' => $param->piutang + $jumlah,
            ]);
    
            // Update total asset
            if ($kasTotalAsset) {
                $kasTotalAsset->update([
                    'saldo' => 
                        optional($kasOnHand)->saldo +
                        optional($kasOperasional)->saldo +
                        optional($kasPiutang)->saldo +
                        optional($kasStock)->saldo -
                        optional($kasUtang)->saldo
                ]);
            }
    
            DB::commit();
            return redirect()->route('piutang.index')->with('success', 'Data piutang berhasil ditambahkan');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error pada PiutangController: ' . $e->getMessage());
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $utang = UtangPiutang::find($id);
        $utang->nominal = number_format($utang->nominal,0,',','.');
        return view('tampilan.keuangan.piutang.update', compact('utang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtangPiutangRequest $request, $id)
    {
        try {
            DB::beginTransaction();
    
            $data = $request->validated();
            $data['jumlah'] = (int) str_replace('.', '', $data['jumlah']);
    
            $base = Kas::whereIn('jenis_kas', ['totalAsset', 'OnHand', 'Operasional', 'Utang', 'Piutang', 'stock'])
                        ->get()
                        ->keyBy('jenis_kas');
    
            $utang = UtangPiutang::find($id);
            
            $data_pelanggan = Pelanggan::where('nama', $utang->nama)->first();
            
            if (!$data_pelanggan) {
                return redirect()->route('piutang.index')->with('error', 'Data pelanggan tidak ditemukan');
            }
    
            $data_lama = $utang->nominal;
            
            // Jika jenisnya sama
            if ($utang->jenis === $data['jenis']) {
                if ($data['jenis'] === 'Piutang') {
                    $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $data_lama + $data['jumlah']]);
                    $data_pelanggan->update([
                        'piutang' => $data_pelanggan->piutang - $data_lama + $data['jumlah']
                    ]);
                } elseif ($data['jenis'] === 'Utang') {
                    $base['Utang']->update(['saldo' => $base['Utang']->saldo - $data_lama + $data['jumlah']]);
                    $data_pelanggan->update([
                        'utang' => $data_pelanggan->utang - $data_lama + $data['jumlah']
                    ]);
                }
            }
            // Jika jenis diubah
            else {
                if ($data['jenis'] === 'Piutang') {
                    $base['Utang']->update(['saldo' => $base['Utang']->saldo - $data_lama]);
                    $base['Piutang']->update(['saldo' => $base['Piutang']->saldo + $data['jumlah']]);
                    $data_pelanggan->update([
                        'utang'   => $data_pelanggan->utang - $data_lama,
                        'piutang' => $data_pelanggan->piutang + $data['jumlah']
                    ]);
                } elseif ($data['jenis'] === 'Utang') {
                    $base['Utang']->update(['saldo' => $base['Utang']->saldo + $data['jumlah']]);
                    $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $data_lama]);
                    $data_pelanggan->update([
                        'piutang' => $data_pelanggan->piutang - $data_lama,
                        'utang'   => $data_pelanggan->utang + $data['jumlah']
                    ]);
                }
                $utang->update(['jenis' => $data['jenis']]);
            }
    
            // Update data utang/piutang
            $utang->update([
                'keterangan' => $data['keterangan'],
                'nominal'    => $data['jumlah'],
            ]);
    
            DB::commit();
            return redirect()->route('piutang.index')->with('success', 'Data utang berhasil diubah');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update data utang/piutang', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'utang_id' => $id,
                'input_data' => $request->all(),
            ]);
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan saat memproses data');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    try {
        DB::beginTransaction();
        $utang = UtangPiutang::find($id);

        if (!$utang) {
            return redirect()->route('piutang.index')->with('error', 'Data Utang Tidak Ditemukan.');
        }

        $data_pelanggan = Pelanggan::where('nama', $utang->nama)->first();
        $base = Kas::whereIn('jenis_kas', ['totalAsset', 'OnHand', 'Operasional', 'Utang', 'Piutang', 'stock'])->get()->keyBy('jenis_kas');

        if ($utang->jenis == 'Piutang') {
            $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $utang->nominal]);
            $data_pelanggan->update([
                'piutang' => $data_pelanggan->piutang - $utang->nominal
            ]);
        } elseif ($utang->jenis == 'Utang') {
            $base['Utang']->update(['saldo' => $base['Utang']->saldo - $utang->nominal]);
            $data_pelanggan->update([
                'utang' => $data_pelanggan->utang - $utang->nominal
            ]);
        }

        $base['totalAsset']->update([
            'saldo' =>
                $base['OnHand']->saldo +
                $base['Operasional']->saldo +
                $base['Piutang']->saldo +
                ($base['stock']->saldo ?? 0) -
                $base['Utang']->saldo
        ]);

        $utang->delete();

        DB::commit();
        return redirect()->route('piutang.index')->with('success', 'Data Utang Berhasil Dihapus.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error pada PiutangController: ' . $e->getMessage());
        return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
    }
}
public function lunas($id)
{
    try {
        DB::beginTransaction();
        $utang = UtangPiutang::find($id);

        if (!$utang) {
            return redirect()->route('piutang.index')->with('error', 'Data tidak ditemukan.');
        }

        $data_pelanggan = Pelanggan::where('nama', $utang->nama)->first();
        $base = Kas::whereIn('jenis_kas', ['totalAsset', 'OnHand', 'Operasional', 'Utang', 'Piutang', 'stock'])->get()->keyBy('jenis_kas');
        if($utang->status == 'Belum Lunas'){
            if ($utang->jenis == 'Utang') {
                $base['Utang']->update(['saldo' => $base['Utang']->saldo - $utang->nominal]);
                $data_pelanggan->update([
                    'utang' => $data_pelanggan->utang - $utang->nominal
                ]);
            } elseif ($utang->jenis == 'Piutang') {
                $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $utang->nominal]);
                $data_pelanggan->update([
                    'piutang' => $data_pelanggan->piutang - $utang->nominal
                ]);
            }
    
            $utang->update(['status' => 'Lunas']);    
        }
        $base['totalAsset']->update([
            'saldo' =>
                $base['OnHand']->saldo +
                $base['Operasional']->saldo +
                $base['Piutang']->saldo +
                ($base['stock']->saldo ?? 0) -
                $base['Utang']->saldo
        ]);
        DB::commit();
        return redirect()->route('piutang.index')->with('success', 'Data Utang sudah Lunas.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error pada PiutangController: ' . $e->getMessage());
        return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
    }
}

}
