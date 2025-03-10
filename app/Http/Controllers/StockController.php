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
       return view('tampilan.penggilingan.stock.index', compact('stock'));
    }
    public function search(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        $query = strtolower($request->input('query'));
        if (!empty($query)) {
            $stock = Stock::where('nama', 'like', '%'.$query.'%')
            ->orWhere('stock', 'like', '%'.$query.'%')
            ->orWhere('harga_satuan', 'like', '%'.$query.'%')
            ->orWhere('total', 'like', '%'.$query.'%')
            ->orderBy('nama')
            ->paginate(10);
            return redirect()->route('penggilingan.stock.index', compact('stock','query'));
            } else {
            $stock = Stock::orderBy('nama')->paginate(10);
            }
            if ($stock->isEmpty()) {
                return redirect()->route('penggilingan.stock.index')->with('error', 'Data Tidak Ada.');
            }
            return view('tampilan.penggilingan.stock.index', compact('stock'));
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = Aset::orderBy('nama')->all();
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockRequest $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //
    }
}
