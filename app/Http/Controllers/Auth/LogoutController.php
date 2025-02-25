<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
     //proses logout
     public function logout(Request $request){
          $user = Auth::user();
      
          Auth::logout();
      
          // Hapus sesi tetapi biarkan cookie remember me jika masih ada
        $request->session()->invalidate();
        $request->session()->regenerateToken();
      
          return redirect('/login')->with('status', 'Berhasil Logout');
      }
     //form login
}
