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
    if (!app('session')->has('user_id')) {
        redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
        exit; // Pastikan eksekusi berhenti di sini
    }

    $tgl = date('Y-m-d');
    $base = kas::where('jenis_kas', 'totalAsset')->first();

    if($base->updated_at->format('Y-m-d') != $tgl){
        $base->update(['saldo_lama'=> $base->saldo]);
    }
    $base->update(['saldo_lama' => $base->saldo]);

    // Ambil data kas dan jadikan key-nya sebagai jenis_kas
    $kasData = kas::whereIn('jenis_kas', [
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

