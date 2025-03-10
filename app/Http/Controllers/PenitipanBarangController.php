<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Models\PenitipanBarang;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePenitipanBarangRequest;
use App\Http\Requests\UpdatePenitipanBarangRequest;

class PenitipanBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PenitipanBarang::join('pelanggans', 'penitipan_barangs.id_pelanggan', '=', 'pelanggans.id')
        ->select('penitipan_barangs.*', 'pelanggans.nama as nama_pelanggan')
        ->orderBy('pelanggans.nama')
        ->paginate(10);

        return view('tampilan.penggilingan.penitipan.penitipan', compact('data'));
    }

    public function search(Request $request)
{
    if (session()->has('error')) {
        session()->forget('error');
    }

    $query = trim(strtolower($request->input('query', ''))); 
    $data = collect(); // Pastikan $data tidak error jika kosong

    if (!empty($query)) {
        $data = PenitipanBarang::join('pelanggans', 'penitipan_barangs.id_pelanggan', '=', 'pelanggans.id')
            ->select('penitipan_barangs.*', 'pelanggans.nama as nama_pelanggan')
            ->where(function($p) use ($query) {
                $p->whereRaw('LOWER(pelanggans.nama) LIKE ?', ['%'.trim($query).'%'])
                  ->orWhereRaw('LOWER(penitipan_barangs.barang) LIKE ?', ['%'.$query.'%'])
                  ->orWhereRaw('LOWER(penitipan_barangs.jumlah) LIKE ?', ['%'.$query.'%'])
                  ->orWhereRaw('LOWER(penitipan_barangs.status) LIKE ?', ['%'.$query.'%']);
            })
            ->orderBy('pelanggans.nama')
            ->paginate(10);
    }

    if ($data->isEmpty()) {
        return redirect()->back()->withErrors('Data not found');
    }

    return view('tampilan.penggilingan.penitipan.penitipan', compact('data', 'query'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function cekPelanggan(Request $request){
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

    public function cekAuto(Request $request){
        $query = $request->query('query');
        $pelanggan = DB::table('pelanggan')->where('nama','LIKE','%'.$query.'%')->limit(5)->get(['nama','alamat']);
        return response()->json($pelanggan);
    }
    
     public function create()
    {
        return view('tampilan.penggilingan.penitipan.penitipan-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenitipanBarangRequest $request)
    {
        $data = $request->validated();
        $jumlah = str_replace('.', '', $data['jumlah']); // Hapus titik pemisah ribuan

        // Simpan atau dapatkan data pelanggan langsung dari firstOrCreate
        $pelanggan = Pelanggan::firstOrCreate(
            ['nama' => $data['nama_pelanggan']],
            [
                'alamat' => $data['alamat_pelanggan'] ?? '',
                'no_telepon' => $data['no_telepon'] ?? '',
            ]
        );

        // Langsung gunakan $pelanggan->id tanpa query tambahan
        PenitipanBarang::create([
            'id_pelanggan' => $pelanggan->id,
            'barang' => $data['barang'],
            'jumlah' => $jumlah, // Tidak perlu ?? '0', karena selalu ada nilai
            'status' => 'Menitipkan'
        ]);     
        return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $penitipan = PenitipanBarang::find($id);
        if (!$penitipan) {
            return redirect()->back()->with('error', 'Data penitipan tidak ditemukan');
        }
        $pelanggan = Pelanggan::find($penitipan->id_pelanggan);
        return view('tampilan.penggilingan.penitipan.penitipan-update', compact('penitipan','pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenitipanBarangRequest $request, $id)
    {
        $data = $request->validated();
        $pelanggan = Pelanggan::find($id);
        $jumlah = str_replace('.','',$data['jumlah']);
        if($pelanggan){
            $penitipan = PenitipanBarang::where('id_pelanggan',$id);
            $penitipan->update([
                'barang' => $data['barang'],
                'jumlah' => $jumlah,
               'status' => $data['status'],
            ]);
    }
    return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil diupdate');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $penitipan = PenitipanBarang::find($id);
        $penitipan->delete();

        return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil dihapus');
    }
}
