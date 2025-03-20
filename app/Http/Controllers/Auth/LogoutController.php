<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;

class LogoutController extends Controller
{
     //proses logout
     public function logout(Request $request)
{
    // Logout user
    Auth::logout();

    // Hapus semua session dan buat token baru
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Hapus cookie remember me (jika ada)
    Cookie::queue(Cookie::forget('remember_username'));
    Cookie::queue(Cookie::forget('remember_password'));

    return redirect('/login')->with('status', 'Berhasil Logout');
}
}
