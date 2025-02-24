<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function getChart(Request $request){
        $jenisKas = $request->query('jenis');
        $mulai = $request->query('mulai');
        $akhir = $request->query('akhir');

        //query berdasarkan jenis data dan rentang waktu
        $data = DB::table('history_kas')
            ->where('tanggal', '>=', $mulai)
            ->where('tanggal', '<=', $akhir)
            ->where('tipe', $jenisKas)
            ->get();

        
        //kirim data chart ke view
        return response()->json([
            'labels' => $data->pluck('tanggal'),
            'values' => $data->pluck('saldo')
        ]);
    }
}
