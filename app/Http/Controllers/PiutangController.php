<?php

namespace App\Http\Controllers;

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
        try{
            DB::beginTransaction();
            $data = $request->validated();
                $data['jumlah'] = str_replace('.','',$data['jumlah']);
                $jumlah = $data['jumlah'];
    
            $param = pelanggan::firstOrCreate([
                [ 'nama' => $data['nama_pelanggan']],
                [ 'alamat' => $data['alamat_pelanggan']],
            ]);
            // Perbarui alamat jika berbeda
            if ($param->alamat !== $data['alamat_pelanggan']) {
                $param->update(['alamat' => $data['alamat_pelanggan']]);
            }
    
            UtangPiutang::create([
                'id_pelanggan' => $param->id,
                'nama' => $data['nama_pelanggan'],
                'alamat' => $data['alamat_pelanggan'],
                'keterangan' => $data['keterangan'],
                'ambil' => $data['ambil'],
                'jenis' => 'Piutang',
                'nominal' => $jumlah,
               'status' => 'Belum Lunas',
            ]);
            //megatur kas di database kas 
            $base = kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional','Utang','Piutang'])->get();
            //juka utang jumlah kurang dari jumlah piutang
            if($base['Utang']->saldo < $jumlah && $base['Utang']->saldo == 0){
                $base['Piutang']->update('saldo', $jumlah - $base['Utang']->saldo);
            }
            //jika pelanggan memiliki utang kurang dari julah
            if($param->utangPiutang == 'Utang' && $param->total < $jumlah){
                $base['Utang']->update('saldo', $base['Utang']->saldo - $param->total);
                $param->update([
                    'utangPiutang' => 'Piutang',
                    'total' => $jumlah - $param->total
                ]);
                $base['Piutang']->update('saldo', $base['Piutang']->saldo + $param->total);
            }else if ($param->utangPiutang == 'Utang' && $param->total > $jumlah){
                $base['Utang']->update('saldo', $base['Utang']->saldo - $jumlah);
                $param->update('total', $param->total - $jumlah);
            }else if($param->utangPiutang == 'Piutang'){
                $base['Piutang']->update('saldo', $base['Piutang']->saldo + $jumlah);
                $param->update('total', $param->total + $jumlah);
            }
            
            if($data['ambil'] == 'OnHand'){
                $base['OnHand']->update('saldo', $base['OnHand']->saldo - $jumlah);
                ArusKas::create([
                    'idKas' => 2, // Pastikan nama kolom benar
                    'keterangan' => 'Piutang',
                    'jenis_kas' => 'OnHand',
                    'jenis_transaksi' => 'Keluar',
                    'jumlah' => $jumlah
                ]);
            }else if($data['ambil'] == 'Operasional'){
                $base['Operasional']->update('saldo', $base['Operasional']->saldo - $jumlah);
                ArusKas::create([
                    'idKas' => 10, // Pastikan nama kolom benar
                    'keterangan' => 'Piutang',
                    'jenis_kas' => 'Operasional',
                    'jenis_transaksi' => 'Keluar',
                    'jumlah' => $jumlah
                ]);
            } //kurang stock belum
           
            $base['totalAsset']->update('saldo', $base['TotalAsset']->saldo - $jumlah);
            DB::commit();
            
            return redirect()->route('piutang.index')->with('success', 'Data utang berhasil ditambahkan');
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }catch(\PDOException $e){
            DB::rollback();
            Log::error('Error pada PiutangController'.$e->getMessage());
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
        return view('tampilan.keuangan.piutang.update', compact('utang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtangPiutangRequest $request, $id)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $base = Kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional','Utang','Piutang'])->get()->keyBy('jenis_kas');
            $data['jumlah'] = str_replace('.','',$data['jumlah']);
    
            $utang = UtangPiutang::find($id);
            $data_pelanggan = Pelanggan::where('nama',$utang->nama)->first();
            if(!$data_pelanggan){
                return redirect()->route('piutang.index')->with('error','Data pelanggan tidak ditemukan');
            }
            if($utang->jenis == $data['jenis'] && $utang->jenis == 'Piutang'){
                if($utang->ambil == $data['ambil'] && $utang->ambil == 'OnHand'){
                    $saldo_pelanggan_lama = $data_pelanggan->total;

                     //Tambah data OnHand
                     $base['OnHand']->create([
                        'idKas' => 2, // Pastikan nama kolom benar
                        'keterangan' => $data['keterangan'],
                        'jenis_kas' => $data['ambil'],
                        'jenis_transaksi' => $data['masuk'],
                        'jumlah' => $data['jumlah'],
                    ]);
                    //tidak lunas -> tidak lunas
                    if($utang->status == 'Tidak Lunas'){                            
                        //piutang -> piutang
                        if($data_pelanggan->utangPiutang == $data['jenis'] && $data['jenis'] == 'Piutang'){
                            if($data['status'] == 'Tidak Lunas'){
                                $data_pelanggan->update([
                                    'total' => $data_pelanggan->total - $utang->nominal + $data['jumlah'],
                                ]);
                                //update kas utama utang
                                $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $utang->nominal - $data['jumlah']]);
                                $base['Piutang']->update(['saldo'=> $base['Piutang']->saldo - $utang->nominal + $data['jumlah']]);   
                            }elseif($data['status'] == 'Lunas'){
                                if($utang->nominal == $data['jumlah']){
                                    $data_pelanggan->update([
                                        'total' => $data_pelanggan->total - $utang->nominal,
                                    ]);
                                    //update kas utama utang
                                    $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $utang->nominal]);
                                    $base['Piutang']->update(['saldo'=> $base['Piutang']->saldo - $utang->nominal]);
                                }else{
                                    $data_pelanggan->update([
                                        'total' => $data_pelanggan->total - $utang->nominal,
                                    ]);
                                    //update kas utama utang
                                    $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $utang->nominal]);
                                    $base['Piutang']->update(['saldo'=> $base['Piutang']->saldo - $utang->nominal]);
                                }
                            }
                            //piutang -> utang
                        }elseif($data_pelanggan->utangPiutang != $data['jenis'] && $data['jenis'] == 'Utang'){
                            //update kas utama utang
                            $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $utang->nominal - $data['jumlah']]);
                            $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $data_pelanggan->total]);
                            $base['Utang']->update([['saldo'] => $base['Utang']->saldo + $data['jumlah'] - $data_pelanggan->total]);
                            $utang->update(['jenis' => 'Utang']);
                            $data_pelanggan->update([
                                'utangPiutang' => 'Utang',
                                'total' => $data['jumlah'],
                            ]);
                        }
                        
                        //bagian bawah direnov
                    //tidak lunas -> lunas
                    }elseif($utang->status == 'Tidak Lunas'){
                        $utang->update(['status' => 'Lunas']); 
                        //piutang -> piutang
                        if($data_pelanggan->utangPiutang == $data['jenis'] && $data['jenis'] == 'Piutang'){
                            //update kas utama utang
                            $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $data['jumlah']]);
                            $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $data['jumlah']]);
                            $data_pelanggan->update([
                                'total' => $data_pelanggan->total - $utang->nominal
                            ]);
                            //piutang -> utang
                        }elseif($data_pelanggan->utangPiutang != $data['jenis'] && $data['jenis'] == 'Utang'){
                            //update kas utama utang
                            $base['OnHand']->update(['saldo' => $base['OnHand']->saldo + $data['jumlah']]);
                            $base['Piutang']->update(['saldo' => $base['Piutang']->saldo - $data_pelanggan->total]);
                            $base['Utang']->update([['saldo'] => $base['Utang']->saldo + $data['jumlah']]);
                            $base['totalAsset']->update(['saldo' => $base['OnHand'] + $base['Operasional'] + $base['Piutang'] - $base['Utang']]);

                            $data_pelanggan->update([
                                'utangPiutang' => 'Utang',
                                'total' => $data['jumlah'],
                            ]);
                    }
            }
            $utang->update([
                'keterangan' => $data['keterangan'],
                'ambil' => $data['ambil'],
                'nominal' => $data['jumlah'],
            ]);
            $base['totalAsset']->update(['saldo' => $base['OnHand'] + $base['Operasional'] + $base['Piutang'] - $base['Utang']]);
        }
                DB::commit();
                return redirect()->route('piutang.index')->with('success', 'Data utang berhasil diubah');
            } else {
                return redirect()->route('piutang.index')->with('error', 'Data utang tidak ditemukan.');
            }
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }catch(\PDOException $e){
            DB::rollback();
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $utang = UtangPiutang::find($id);
            if ($utang === null) {
                return redirect()->route('piutang.index')->with('error', 'Data Utang Tidak Ditemukan.');
            }
            $utang->delete();
            return redirect()->route('piutang.index')->with('success', 'Data Utang Berhasil Dihapus.');
           }catch(\Exception $e){
            Log::error('Error pada PiutangController'.$e->getMessage());
            return redirect()->route('piutang.index')->with('error', 'Terjadi kesalahan pada server');
           }
        }
}
