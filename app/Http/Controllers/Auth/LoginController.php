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
       $request->validate([
        'username' =>'required|string|max:255',
        'password' => 'required|string|min:4'
       ]);

       $remember = $request->has('remember');

       $credentials =[
        'username' => $request->username,
        'password' => $request->password,
       ];

       if (Auth::attempt($credentials, $remember)){
        session(['key' => 'true']);

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        if ($remember) {
               // Simpan username dan password ke dalam cookie selama 7 hari
               Cookie::queue('remember_username', $request->username, 10080);
               Cookie::queue('remember_password', $request->password, 10080);
          } else {
               // Hapus cookie jika remember tidak dicentang
               Cookie::queue(Cookie::forget('remember_username'));
               Cookie::queue(Cookie::forget('remember_password'));
          }

        return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang');
     }

     return redirect()->back()->with('error', 'Login Gagal');
}
}
