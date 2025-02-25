<?php

namespace App\Http\Controllers;

use App\Models\ArusKas;
use App\Http\Requests\StoreArus_KasRequest;
use App\Http\Requests\UpdateArus_KasRequest;

class ArusKasController extends Controller
{
    public function index(){
        $arus_kas = ArusKas::all();
        return view('/dashboard/laporan/arus_kas', compact('arus_kas'));
    }
}
