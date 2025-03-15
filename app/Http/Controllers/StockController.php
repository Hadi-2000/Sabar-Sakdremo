<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $stock = Stock::orderBy('nama')->paginate(10);
       $stockNama = $stock->pluck('nama');
       $produk = Aset::whereIn('nama',$stockNama)->orderBy('nama')->paginate(10);
       return view('tampilan.penggilingan.stock.index', compact('stock','produk'));
    }
    public function search(Request $request) {
        // Hapus session error jika ada
        session()->forget('error');
    
        // Ambil query pencarian dan ubah ke lowercase
        $query = strtolower($request->input('query'));
    
        if (!empty($query)) {
            // Filter data berdasarkan nama, stock, atau total
            $stock = Stock::where('nama', 'like', '%'.$query.'%')
                ->orWhere('stock', 'like', '%'.$query.'%')
                ->orWhere('total', 'like', '%'.$query.'%')
                ->orderBy('nama')
                ->paginate(10);

                $stockNama = $stock->pluck('nama');
                $produk = Aset::whereIn('nama',$stockNama)->orderBy('nama')->paginate(10);
    
            // Jika tidak ada hasil, kembalikan dengan pesan error
            if ($stock->isEmpty()) {
                return redirect()->route('stock.index')->with('error', 'Data Tidak Ada.');
            }
    
            // Kirim query sebagai parameter dalam URL agar tetap muncul di input pencarian
            return view('tampilan.penggilingan.stock.index', compact('stock', 'query', 'produk'));
        }
    
        // Jika query kosong, tampilkan semua stock tanpa filter
        return redirect()->route('stock.index')->with('error', 'Masukkan kata kunci pencarian.');
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = Aset::orderBy('nama')->get();
        return view('tampilan.penggilingan.stock.create', compact('produk'));
    }
    public function cek(Request $request){
        try {
            $request->validate([
                'nama' => 'required|string|min:3',
            ]);
    
            $produk = Aset::where('nama', $request->nama_pelanggan)->first();
    
            return response()->json([
                'ada' => $produk ? true : false
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function cekAuto(Request $request){
        $query = $request->query('query');
        $produk = DB::table('aset')->where('nama','LIKE','%'.$query.'%')->limit(5)->get(['nama']);
        return response()->json($produk);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request)
    {
        $data = $request->validated();
        //jumlah
        $jumlah = str_replace('.','', $data['jumlah']);
        //ambil data produk 
        $produk = Aset::where('nama', $data['nama'])->first();

        // insert data stock
        Stock::create([
            'product_id' => $produk->id,
            'nama' => $data['nama'],
           'stock' => $jumlah,
            'total' => $jumlah * $produk->harga_satuan
        ]);

        return redirect()->route('stock.index')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        $produk = Aset::orderBy('nama')->get(); // Ambil semua produk dari tabel Aset
    
        return view('tampilan.penggilingan.stock.update', compact('stock', 'produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $data = $request->validated();
        // jumlah
        $jumlah = str_replace('.','', $data['jumlah']);
        //ambil data produk 
        $produk = Aset::where('nama', $data['nama'])->first();

        // update data stock
        $stock->update([
            'product_id' => $produk->id,
            'nama' => $data['nama'],
           'stock' => $jumlah,
            'total' => $jumlah * $produk->harga_satuan
        ]);

        return redirect()->route('stock.index')->with('success', 'Data berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete(); // Hapus 1 data berdasarkan ID
        return redirect()->route('stock.index')->with('success', 'Data berhasil dihapus.');
    }
}
