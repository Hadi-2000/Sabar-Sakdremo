<?php

namespace App\Http\Controllers;

use App\Models\kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function viewDashboard()
{
    // Pastikan user login
    if (!session()->has('key')) {
        return redirect()->back()->with('error', 'Anda harus login terlebih dahulu');
    }

    // Ambil data kas dan jadikan key-nya sebagai jenis_kas
    $kasData = Kas::whereIn('jenis_kas', [
        'totalAsset', 'OnHand', 'Operasional', 'Stock',
        'Utang', 'Piutang', 'labaBersih', 'labaKotor',
        'pengeluaran', 'selisih'
    ])->get()->keyBy('jenis_kas');

    //   // Hitung total asset
    // $AssetFix = ($kasData['OnHand']->saldo ?? 0) 
    //           + ($kasData['Operasional']->saldo ?? 0) 
    //           + ($kasData['Stock']->saldo ?? 0) 
    //           + ($kasData['Piutang']->saldo ?? 0) 
    //           - ($kasData['Utang']->saldo ?? 0);

    // // Jika total asset tidak sesuai, perbarui saldo
    // if (($kasData['totalAsset']->saldo ?? 0) != $AssetFix) {
    //     $kasData['totalAsset']->update(['saldo' => $AssetFix]);
    //     $kasData = Kas::whereIn('jenis_kas', [
    //         'totalAsset', 'OnHand', 'Operasional', 'Stock',
    //         'Utang', 'Piutang', 'labaBersih', 'labaKotor',
    //         'pengeluaran', 'selisih'
    //     ])->get()->keyBy('jenis_kas');
    // }

    // Tampilkan halaman dashboard dengan data kas
    return view('tampilan.dashboard', compact('kasData'));
}

    }

