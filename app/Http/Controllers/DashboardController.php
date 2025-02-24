<?php

namespace App\Http\Controllers;

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
    // Ambil data pelanggan melalui database yang lain (kas)
    $totalAsset = kas::where('jenis_kas', 'totalAsset');

    // Menampilkan halaman dashboard dengan data session
    return view('tampilan.dashboard', ['totalAsset'=> $totalAsset]);
}
    }

