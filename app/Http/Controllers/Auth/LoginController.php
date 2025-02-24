<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class LoginController extends Controller
{
     //proses login
     public function login(Request $request){

       $request->validate([
        'username' =>'required|string|max:255',
        'password' => 'required|string|min:4'
       ]);

       $remember = $request->hash('remember');

       $credentials =[
        'username' => $request->username,
        'password' => $request->password,
       ];

       if (Auth::attempt($credentials, $remember)){
        session(['key' => 'true']);
        return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang');

     }
     return redirect()->back()->with('error', 'Login Gagal');
}
}
