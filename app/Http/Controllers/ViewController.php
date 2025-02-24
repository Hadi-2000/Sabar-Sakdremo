<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller {
    protected $errorMessage;

    public function __construct() {
        $this->errorMessage = 'Anda harus login terlebih dahulu';
    }

    public function ViewLogin() {
        return view('login');
    }

    private function checkSession()
    {
        if (!session()->has('key')) {
            return redirect()->route('/login')->with('error', $this->errorMessage);
        }
        return null;
    }

    public function viewKeuanganKas()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/keuangan/kas');
    }

    public function viewKeuanganUtang()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/keuangan/utang');
    }

    public function viewKeuanganPiutang()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/keuangan/piutang'); // typo diperbaiki
    }
    public function viewLaporanArusKas()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/laporan/arus_kas'); // typo diperbaiki
    }
    public function viewLaporanLabaRugi()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/laporan/laba_rugi'); // typo diperbaiki
    }
    public function viewLaporanUtangPiutang()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/laporan/utang_piutang'); // typo diperbaiki
    }
    public function viewLaporanStock()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/laporan/stock'); // typo diperbaiki
    }
    public function viewPenggilinganPelanggan()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/penggilingan/pelanggan'); // typo diperbaiki
    }
    public function viewPenggilinganTenagaKerja()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/penggilingan/tenaga_kerja'); // typo diperbaiki
    }
    public function viewPenggilinganPenitipan()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/penggilingan/penitipan'); // typo diperbaiki
    }
    public function viewPenggilinganMesin()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/penggilingan/mesin'); // typo diperbaiki
    }
    public function viewPenggilinganPerbaikan()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/penggilingan/perbaikan'); // typo diperbaiki
    }
    public function viewProfile()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/profile/profile'); // typo diperbaiki
    }
    public function viewProfilePengaturan()
    {
        if ($redirect = $this->checkSession()){return $redirect;}
        return view('tampilan/profile/pengaturan'); // typo diperbaiki
    }

    //ingat ini belum selesai kamu belum memperaiki yang penggilingn dan profil
    //dan mengtue nvbar
}