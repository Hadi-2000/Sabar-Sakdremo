<?php

namespace App\Http\Controllers;

use App\Models\kas;
use App\Models\ArusKas;
use App\Models\Pegawai;
use App\Jobs\UpdateKasJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function viewDashboard()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }
            $tgl = date('Y-m-d');
    
            $kas2 = kas::whereIn('jenis_kas', [
                'totalAsset', 'OnHand', 'Operasional', 'Stock',
                'Utang', 'Piutang', 'labaBersih', 'labaKotor',
                'pengeluaran', 'selisih', 'pemasukan'
            ])->get()->keyBy('jenis_kas');
             UpdateKasJob::dispatch();
            return view('tampilan.dashboard', compact('kas2'));
    }
}