<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;


class LoginController extends Controller
{
     //proses login
     public function login(Request $request){
        if (Cookie::has('remember_token')) {
            try {
                $token = decrypt(Cookie::get('remember_token'));
                [$username, $timestamp] = explode('|', $token);
                $user = User::where('username', $username)->first();
                
                if ($user) {
                    Auth::login($user, true);
                    session(['user_id' => $user->id]);
                    $request->session()->regenerate();
                    return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang Kembali');
                }
            } catch (\Exception $e) {
                Cookie::queue(Cookie::forget('remember_token')); // Jika token salah, hapus
            }
        }
    
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:4'
        ]);
    
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];
    
        if (Auth::attempt($credentials, $request->has('remember'))) {
            session(['user_id' => Auth::user()->id]);
            $request->session()->regenerate();
    
            if ($request->has('remember')) {
                $token = encrypt($request->username . '|' . now());
                Cookie::queue('remember_token', $token, 10080);
            } else {
                Cookie::queue(Cookie::forget('remember_token'));
            }
    
            return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang');
        }
    
        return redirect()->back()->with('error', 'Login Gagal');
    }
}    
