<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\ArusKas;
use App\Models\Pelanggan;
use App\Models\UtangPiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facedes\view;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class UtangPiutangController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
   // Utang
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
    //fubnction cek auto 
    public function cekAuto(Request $request){
        try{
            $query = $request->query('query');
            $pelanggan = DB::table('pelanggan')->where('nama','LIKE','%'.$query.'%')->limit(5)->get(['nama','alamat']);
            return response()->json($pelanggan);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
}
}