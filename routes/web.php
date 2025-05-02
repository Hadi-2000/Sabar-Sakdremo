<?php

use App\Models\Aset;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UtangController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UtangPiutangController;
use App\Http\Controllers\PenitipanBarangController;

//tampilan login log out
Route::get('/', [ViewController::class, 'viewLogin']);
Route::get('/login',  [ViewController::class, 'viewLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

//tampilan dashboard
Route::middleware('auth','throttle:100,1')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'viewDashboard']);

    //tampilan keuangan kas
    Route::resource('/keuangan/kas', ArusKasController::class)->except(['show']);
    Route::get('/dashboard/keuangan/kas/search', [ArusKasController::class, 'search'])->name('keuangan.kas.search');

    //tampilan keuangan Utang
    Route::resource('/keuangan/utang', UtangController::class)->except(['show']);
    Route::get('/dashboard/keuangan/utang/lunas/{id}', [UtangController::class, 'lunas'])->name('keuangan.utang.lunas');
    Route::get('/dashboard/keuangan/utang/search', [UtangController::class, 'search'])->name('keuangan.utang.search');
    
    //tampilan keuangan piutang
    Route::resource('/keuangan/piutang', PiutangController::class)->except(['show']);
    Route::get('/dashboard/keuangan/piutang/lunas/{id}', [PiutangController::class, 'lunas'])->name('keuangan.piutang.lunas');
    Route::get('/dashboard/keuangan/piutang/search', [PiutangController::class, 'search'])->name('keuangan.piutang.search');
    
    Route::get('/dashboard/keuangan/utang/create/cek-auto', [UtangPiutangController::class,'cekAuto'])->name('keuangan.utang.create.cek-auto');
    Route::get('/dashboard/keuangan/utang/create/cek-pelanggan', [UtangPiutangController::class,'checkPelanggan'])->name('keuangan.utang.create.cek-pelanggan');

    //tampilan laporan kas
    Route::get('/dashboard/laporan/arus_kas', [LaporanController::class, 'LaporanArusKas'])->name('laporan.kas.index');
    Route::get('/dashboard/laporan/arus_kas/search', [LaporanController::class, 'LaporanArusKasSearch'])->name('laporan.kas.search');
    Route::get('/dashboard/laporan/arus_kas/download', [LaporanController::class, 'LaporanArusKasDownload'])->name('laporan.kas.download');

    Route::get('/dashboard/laporan/utang_piutang', [LaporanController::class, 'LaporanUtangPiutang'])->name('laporan.utangPiutang.index');
    Route::get('/dashboard/laporan/laba_rugi', [LaporanController::class, 'LaporanLabaRugi'])->name('laporan.labaRugi.index');

    //tampilan laporan stock
    Route::get('/dashboard/laporan/stock', [LaporanController::class, 'LaporanStock'])->name('laporan.stock');
    Route::get('/dashboard/laporan/stock/download', [LaporanController::class, 'LaporanStockDownload'])->name('laporan.stock.download');

    //tampilan penggilingan
    //tampilan pelanggan
    Route::resource('/penggilingan/pelanggan', PelangganController::class)->except(['show']);
    Route::get('/pelanggan/search', [PelangganController::class, 'search'])->name('penggilingan.pelanggan.search');

    //tenga kerja list
    Route::resource('/penggilingan/tenaga_kerja', PegawaiController::class)->except(['show']);
    Route::get('/penggilingan/tenaga_kerja/search',[PegawaiController::class, 'search'])->name('tenaga_kerja.search');
    Route::get('/penggilingan/tenaga_kerja/hadir/{id}', [PegawaiController::class, 'hadir'])->name('penggilingan.tenaga_kerja.hadir');
    Route::get('/penggilingan/tenaga_kerja/bayar-gaji/{id}', [PegawaiController::class, 'bayar_gaji'])->name('penggilingan.tenaga_kerja.bayar_gaji');
    Route::get('/penggilingan/tenaga_kerja/tidak_hadir/{id}', [PegawaiController::class, 'tidakHadir'])->name('penggilingan.tenaga_kerja.tidak_hadir');

    //penitipan
    Route::resource('/penggilingan/penitipan', PenitipanBarangController::class)->except(['show']);
    Route::get('/dashboard/penggilingan/penitipan/search', [PenitipanBarangController::class, 'search'])->name('penggilingan.penitipan.search');
    Route::get('/dashboard/penggilingan/penitipan/create/cek-pelanggan', [PenitipanBarangController::class, 'cekPelanggan'])->name('penggilingan.penitipan.create.cek-pelanggan');
    Route::get('/dashboard/penggilingan/penitipan/create/cek-auto', [PenitipanBarangController::class, 'cekAuto'])->name('penggilingan.penitipan.create.cek-auto');

    //Aset
    Route::resource('/penggilingan/aset', AsetController::class)->except(['show']);
    Route::get('/dashboard/penggilingan/aset/search', [AsetController::class,'search'])->name('penggilingan.aset.search');

    //stock
    Route::resource('/penggilingan/stock', StockController::class)->except(['show']);
    Route::get('/dashboard/penggilingan/stock/search', [StockController::class,'search'])->name('penggilingan.stock.search');
    Route::get('/dashboard/penggilingan/stock/create/cek', [StockController::class, 'cek'])->name('penggilingan.stock.create.cek');
    Route::get('/dashboard/penggilingan/stock/create/cek-auto', [StockController::class, 'cekAuto'])->name('penggilingan.stock.create.cek-auto');

    //mesin
    Route::resource('/penggilingan/mesin', MesinController::class)->except(['show']);
    Route::get('/dashboard/penggilingan/mesin/search', [MesinController::class,'search'])->name('penggilingan.mesin.search');

    //perbaikan
    Route::resource('/dashboard/penggilingan/perbaikan', PerbaikanController::class)->except(['show']);
    Route::get('/dashboard/penggilingan/perbaikan/search', [PerbaikanController::class,'search'])->name('penggilingan.perbaikan.search');

    //tampilan profil pengguna
    Route::get('/dashboard/profile', [ProfileController::class,'index'])->name('profile.index');
    Route::get('/dashboard/profile/edit/data', [ProfileController::class,'editData'])->name('profile.edit.data');
    Route::post('/dashboard/profile/edit/data', [ProfileController::class,'updateData'])->name('profile.update.data');
    Route::get('/dashboard/profile/edit/password', [ProfileController::class,'editPassword'])->name('profile.edit.password');
    Route::post('/dashboard/profile/edit/password', [ProfileController::class,'updatePassword'])->name('profile.update.password');
    Route::get('/dashboard/profil/pengaturan', [ViewController::class, 'viewProfilePengaturan']);

});
