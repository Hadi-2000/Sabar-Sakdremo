<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\kas;
use App\Models\ArusKas;
use App\Models\Pegawai;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;

class PegawaiController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    try {
        $tanggal = date('Y-m-d');
        DB::beginTransaction();

        $pegawaiList = Pegawai::all();

        foreach ($pegawaiList as $p) {
            if ($p->updated_at->format('Y-m-d') !== $tanggal) {
                if ($p->kehadiran === 'Pulang' && $p->cek_in && $p->cek_out) {
                    $beban_gaji = 0;

                    if ($p->cek_in >= '08:00:00' && $p->cek_in <= '09:00:00') {
                        if ($p->cek_out >= '11:30:00' && $p->cek_out <= '13:00:00') {
                            $beban_gaji = $p->gaji / 2;
                        } elseif ($p->cek_out >= '15:00:00' && $p->cek_out <= '18:00:00') {
                            $beban_gaji = $p->gaji;
                        }
                    } elseif ($p->cek_in >= '11:30:00' && $p->cek_in <= '13:00:00') {
                        if ($p->cek_out >= '15:00:00' && $p->cek_out <= '18:00:00') {
                            $beban_gaji = $p->gaji / 2;
                        }
                    }

                    $p->update([
                        'beban_gaji' => $p->beban_gaji + $beban_gaji
                    ]);
                }

                // Reset harian
                $p->update([
                    'kehadiran' => 'Tidak Hadir',
                    'cek_in' => null,
                    'cek_out' => null
                ]);
            }
            $p->update([
                'kehadiran' => 'Tidak Hadir',
                'cek_in' => null,
                'cek_out' => null
            ]);
        }

        $pegawai = Pegawai::orderBy('nama')->paginate(10);
        DB::commit();
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja', compact('pegawai'));
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error pada PegawaiController@index : ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
    }
}


    public function search(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        try{
            $query = trim(strtolower(strip_tags($request->validate([
                'query' => 'nullable|string|min:1|max:255'
            ])['query'] ?? '')));

            if (!empty($query)) {
                $pegawai = Pegawai::where(function($p) use ($query) {
                    $p->where('nama', 'like', "%{$query}%")
                    ->orWhere('status', 'like', "%{$query}%")
                    ->orWhere('alamat', 'like', "%{$query}%")
                    ->orWhere('kehadiran', 'like', "%{$query}%")
                    ->orWhere('gaji', 'like', "%{$query}%");
                })->orderBy('nama')->paginate(10);
            }else{
                $pegawai = Pegawai::orderBy('nama')->paginate(10);
            }
            if($pegawai->isEmpty()) {
                return redirect()->back()->withErrors('Data not found');
            }
            return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja',compact('pegawai','query'));
        }catch(\Exception $e){
            Log::error('Error pada PegawaiController@search : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
        
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePegawaiRequest $request)
    {
        try{
            $data = $request->validated();
            $param = Pegawai::where('nama',$data['nama'])
            ->Where('alamat',$data['alamat'])->first();

            $jumlah = str_replace('.','',$data['jumlah_hidden']);
            DB::beginTransaction();

            if($param){
                return back()->with('error','Data Pegawai Sudah Ada');
            }else{
                Pegawai::create([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'no_telp' => $data['no_telp'] ?? '',
                    'status' => 'Aktif',
                    'kehadiran' => 'Tidak Hadir',
                    'gaji' => $jumlah,
                    ]);
            }
            $pelanggan = Pelanggan::where('nama',$data['nama'])
            ->where('alamat', $data['alamat'])->first();

            if(!$pelanggan){
                Pelanggan::create([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'] ?? '',
                    'no_telepon' => $data['no_telp'] ?? '',
                ]);
            }

            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Ditambahkan');    
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@store : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $pegawai = Pegawai::where('id',$id)->first();
        $pegawai->gaji = number_format($pegawai->gaji, 0, ',', '.');
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja-update', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        //dd($request->no_telp);
        try{
            $data = $request->validated();

            DB::beginTransaction();

            $jumlah = str_replace('.','',$data['jumlah_hidden']);
            $pelanggan = Pelanggan::where('nama',$pegawai->nama)->first();

            $pegawai->where('id',$data['id'])->update([
                'nama' => $data['nama'],
                'alamat' => $data['alamat'],
                'no_telp' => $data['no_telp'],
                'gaji' => $jumlah
            ]);
            if($pelanggan){
                $pelanggan->update([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'] ?? '',
                    'no_telepon' => $data['no_telp'] ?? '',
                ]);
            }else{
                $pelanggan->create([
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'] ?? '',
                    'no_telepon' => $data['no_telp'] ?? '',
                ]);
            }
            
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@update : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            Pegawai::where('id',$id)->delete();
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Dihapus');
        }catch(\Exception $e){
            Log::error('Error pada PegawaiController@destroy : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
    }
     public function hadir($id){
        try{
            DB::beginTransaction();
            $jam = Carbon::now()->format('H:i:s');
            Pegawai::where('id',$id)->update([
                'kehadiran' => 'Hadir',
                'cek_in' => $jam
            ]);
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@hadir : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
     }
     public function tidakHadir($id){
        try{
            DB::beginTransaction();
            $jam = Carbon::now()->format('H:i:s');
            Pegawai::where('id',$id)->update([
                'kehadiran' => 'Pulang',
                'cek_out' => $jam
            ]);
            DB::commit();
            return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Error pada PegawaiController@tidakHadir : '.$e->getMessage());
            return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
        }
}
public function bayar_gaji($id){
    try{
        DB::beginTransaction();
        $pegawai =Pegawai::where('id',$id)->first();
        $kas = kas::whereIn('jenis_kas',['totalAsset','OnHand','Operasional','Utang','Piutang'])->get();
        if($kas['Operasional'] > $pegawai->beban_gaji){
            $kas['Operasional']->update([
                'saldo' => $kas['Operasioal']->saldo - $pegawai->beban_gaji
            ]);
            ArusKas::create([
                'idKas' => 10, // Pastikan nama kolom benar
                'keterangan' => 'gajian tenaga kerja',
                'jenis_kas' => 'Operasioal',
                'jenis_transaksi' =>'Keluar',
                'jumlah' => $pegawai->beban_gaji,
            ]);
            $kas['totalAsset']->update([
                'saldo' => $kas['totalAsset']->saldo -  $pegawai->beban_gaji
            ]);
            $pegawai->update([
                'beban_gaji' => ''
            ]);
        }
        DB::commit();
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
    }catch(\Exception $e){
        DB::rollBack();
        Log::error('Error pada PegawaiController@tidakHadir : '.$e->getMessage());
        return redirect()->back()->with('error', 'Terdapat kesalahan pada server');
    }
}
}