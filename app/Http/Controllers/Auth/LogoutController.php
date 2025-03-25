<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Hapus token "Remember Me" di database jika ada
        if (Auth::check()) {
            // $user = Auth::user();
            // $user->remember_token = null;
            // $user->save();
            $userId = Auth::id(); // Ambil ID user yang sedang login
            DB::table('users')->where('id', $userId)->update(['remember_token' => null]);
        }

        // Logout user dari sistem
        Auth::logout();

        // Hapus semua session dan buat session baru
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->regenerate(); // Tambahan untuk menghindari session hijacking

        // Hapus cookie remember me jika ada
        Cookie::queue(Cookie::forget('remember_token'));

        return redirect('/login')->with('status', 'Berhasil Logout');
    }
}

