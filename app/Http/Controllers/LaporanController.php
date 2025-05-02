<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArusKas;
use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf; 

class LaporanController extends Controller
{
    public function LaporanArusKas()
    { 
        $arus = ArusKas::orderBy('updated_at','ASC')->get();
        foreach($arus as $item){
            if ($item->updated_at) {
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d');
            } else {
                $item->updated_at = ''; // atau kosong '' kalau mau
            }
        }
        return view('tampilan/laporan/arus_kas',compact('arus'));
    }
    public function LaporanArusKasSearch(Request $request){
        $request->validate([
            'start_date' => 'min:1',
            'end_date' => 'min:1'
        ]);
        $query = ArusKas::orderBy('updated_at','asc');
        foreach($query as $item){
            if ($item->updated_at) {
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d');
            } else {
                $item->updated_at = ''; // atau kosong '' kalau mau
            }
        }
        if($request->start_date && $request->end_date){
            $query->whereBetween('updated_at',[$request->start_date,$request->end_date]);
        }
        $arus = $query->get();
        $start = $request->start_date;
        $end = $request->end_date;
        return view('tampilan/laporan/arus_kas',compact(['arus','start','end']));
    }
    public function LaporanArusKasDownload(Request $request){
    $query = ArusKas::orderBy('updated_at', 'ASC');

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
    }

    $arus = $query->get();

    foreach($arus as $item){
        $item->updated_at = \Carbon\Carbon::parse($item->updated_at->format('Y-m-d'));
    }
    // Load view khusus untuk PDF
    $pdf = Pdf::loadView('tampilan.laporan.pdf.laporan_arus_kas', compact('arus'));

    return $pdf->download('laporan_arus_kas.pdf');
    }
    public function LaporanLabaRugi()
    {
        return view('tampilan/laporan/laba_rugi'); // typo diperbaiki
    }
    public function LaporanUtangPiutang()
    {
        return view('tampilan/laporan/utang_piutang'); // typo diperbaiki
    }
    public function LaporanStock()
    {
        $stock = Stock::orderBy('nama','ASC')->get();
        foreach($stock as $item){
            if ($item->updated_at) {
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d');
            } else {
                $item->updated_at = ''; // atau kosong '' kalau mau
            }
        }
        return view('tampilan/laporan/stock', compact('stock')); // typo diperbaiki
    }
    public function LaporanStockDownload(){
    $tgl = date('Y-m-d');
    $stock = Stock::orderBy('nama','asc')->get();
    foreach($stock as $item){
        if ($item->updated_at) {
            $item->updated_at = \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d');
        } else {
            $item->updated_at = ''; // atau kosong '' kalau mau
        }
    }
    $pdf = Pdf::loadView('tampilan.laporan.pdf.laporan_stock', compact(['tgl','stock']));
    return $pdf->download('laporan_stock.pdf',compact(['tgl','stock']));
    }

}
