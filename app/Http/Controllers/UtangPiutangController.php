<?php

namespace App\Http\Controllers;

use App\Models\UtangPiutang;
use App\Models\Kas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUtangPiutangRequest;
use App\Http\Requests\UpdateUtangPiutangRequest;

class UtangPiutangController extends Controller
{
    private const VALIDATION_RULE_STRING = 'request|string';
   // Utang
    public function indexUtang()
    {
        $utang = UtangPiutang::where('jenis','Utang')->orderBy('created_at', 'desc')->get();
        return view('tampilan.keuangan.utang', compact('utang'));
    }
    public function searchUtang(Request $request){
        if(session()->has('error')){
            session()->forget('error');
        }
        $query = $request->input('query');
        
        $isDate = strtotime($query) !== false;

        $utang = UtangPiutang::where('jenis','Utang');

        if(!empty($query)){
            $utang->where(function($q) use ($query, $isDate) {
                $q->where('keterangan', 'LIKE', '%'.$query.'%')
                  ->orWhere('nama', 'LIKE', '%'.$query.'%')
                  ->orWhere('alamat', 'LIKE', '%'.$query.'%')
                  ->orWhere('jenis','LIKE', '%'.$query.'%')
                  ->orWhere('nominal','LIKE','%'.$query.'%')
                  ->orWhere('status','LIKE','%'.$query.'%')
                  ->orWhere('created_at','LIKE','%'.$query.'%');

                  if($isDate && strtotime($query) !== false){
                    $q->orWhereDate('created_at', '=', date('Y-m-d H:i:s', strtotime($query)));
                  }
            });

            $utang = $utang->orderBy('id','desc')->get();

            if($utang->isEmpty()){
                return redirect()->route('keuangan.utang.index')->with('error','Data Tidak Ada.');
            }
            return view('tampilan.keuangan.utang',compact('utang','query'));
        }
}

    public function createIndexUtang(){
        return view('tampilan.keuangan.utang-create');
    }
    public function createUtang(Request $request){
        $request->validate([
            'nama_pelanggan' => self::VALIDATION_RULE_STRING,
            'alamat_pelanggan' => self::VALIDATION_RULE_STRING,
            'jumlah_hidden' => 'required|numeric|min:1',
           'status' => self::VALIDATION_RULE_STRING
        ]);

        $utang = Kas::where('jenis_kas','Utang')->first();
        $totalAsset = Kas::where('jenis_kas','totalAsset')->first();

        
        return view('tampilan.keuangan.utang-create');
    }
//piutang
    public function indexPiutang()
    {
        $piutang = UtangPiutang::where('jenis','Piutang')->orderBy('created_at', 'desc')->get();
        return view('tampilan.keuangan.piutang', compact('piutang'));
    }
}