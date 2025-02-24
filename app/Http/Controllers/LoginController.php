<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
     //proses login
     public function Login(Request $request){

       $request->validate([
        'username' =>'required|string|max:255',
        'password' => 'required|string|min:4'
       ]);

       $credentials =[
        'username' => $request->username,
        'password' => $request->password,
       ];

       if (Auth::attempt($credentials)){
        session(['key' => 'true']);
        return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang');

     }
     return redirect()->back()->with('error', 'Login Gagal');
}

     //proses logout
     public function logout(Request $request){
      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      return redirect('/login')->with('status', 'Berhasil Logout');
     }

     //form login
}
