<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = Pegawai::orderBy('nama')->paginate(10);
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja',compact('pegawai'));
    }

    public function search(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        $query = strtolower($request->input('query'));

        if (!empty($query)) {
            $pegawai = Pegawai::where(function($p) use ($query) {
                $p->where('nama', 'like', '%'.$query.'%')
                ->orWhere('status', 'like', '%'.$query.'%')
                ->orWhere('alamat', 'like', '%'.$query.'%')
                ->orWhere('kehadiran', 'like', '%'.$query.'%')
                ->orWhere('gaji', 'like', '%'.$query.'%');
            })->orderBy('nama')->paginate(10);
        }else{
            $pegawai = Pegawai::orderBy('nama')->paginate(10);
        }
        if($pegawai->isEmpty()) {
            return redirect()->back()->withErrors('Data not found');
        }
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja',compact('pegawai','query'));
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
        $data = $request->validated();
        $param = Pegawai::where('nama',$data['nama'])
        ->Where('alamat',$data['alamat'])->first();

        $jumlah = str_replace('.','',$data['jumlah_hidden']);

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
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        return view('tampilan.penggilingan.tenaga_kerja.tenaga_kerja-update', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        $data = $request->validated();
        $cek = Pegawai::where('id','!=',$pegawai->id)
        ->where('nama',$data['nama'])
        ->where('alamat',$data['alamat'])->first();

        if($cek){
            return back()->with('error','Data Pegawai Sudah Ada');
        }
        $jumlah = str_replace('.','',$data['jumlah_hidden']);
        $pegawai->update([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'no_telp' => $data['no_telp']?? '',
            'gaji' => $jumlah
        ]);
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Dihapus');
    }
     public function hadir(Pegawai $pegawai){
        $pegawai->update([
            'kehadiran' => 'Hadir'
        ]);
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
     }
     public function tidakHadir(Pegawai $pegawai){
        $pegawai->update([
            'kehadiran' => 'Pulang'
        ]);
        return redirect()->route('tenaga_kerja.index')->with('success','Data Pegawai Berhasil Diubah');
}
}