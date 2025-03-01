<?php

namespace App\Http\Controllers;

use App\Models\kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function viewDashboard()
    { // Pastikan hanya user login yang bisa akses
        // Pastikan hanya user login yang bisa akses
    if (!session()->has('key')) {
        return redirect()->back()->with('error', 'Anda harus login terlebih dahulu');
    }
     // Ambil data kas berdasarkan jenis_kas yang diperlukan
     $kasData = Kas::whereIn('jenis_kas', [
        'totalAsset', 'OnHand', 'Operasional', 'Stock',
        'Utang', 'Piutang', 'labaBersih', 'labaKotor',
        'pengeluaran', 'selisih'
    ])->get()->groupBy('jenis_kas');

    // Menampilkan halaman dashboard dengan data kas yang telah dikelompokkan
    return view('tampilan.dashboard', compact('kasData'));
}
    }

