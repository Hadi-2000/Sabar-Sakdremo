<?php

namespace App\Http\Controllers\Auth;

use App\Models\kas;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\StoreloginRequest;
use App\Http\Requests\UpdateloginRequest;


class LoginController extends Controller
{
    public function __construct()
    {
        // Auto-login jika remember_token ada
        if (Cookie::has('remember_token')) {
            $this->autoLogin();
        }
        session()->invalidate();
        session()->regenerateToken();
        session()->regenerate(); // Tambahan untuk menghindari session hijacking

        // Hapus cookie remember me jika ada
        $data = kas::all();
        if($data->isEmpty()){
            kas::insert([
                ['jenis_kas' => 'totalAsset', 'saldo' => 0],
                ['jenis_kas' => 'OnHand', 'saldo' => 0],
                ['jenis_kas' => 'Operasional', 'saldo' => 0],
                ['jenis_kas' => 'Utang', 'saldo' => 0],
                ['jenis_kas' => 'Piutang', 'saldo' => 0],
                ['jenis_kas' => 'Stock', 'saldo' => 0],
                ['jenis_kas' => 'Pemasukan', 'saldo' => 0],
                ['jenis_kas' => 'Pengeluaran', 'saldo' => 0],
                ['jenis_kas' => 'labaKotor', 'saldo' => 0],
                ['jenis_kas' => 'labaBersih', 'saldo' => 0],
                ['jenis_kas' => 'selisih', 'saldo' => 0],
            ]);
        }
        $admin = User::where('username', 'Admin1234')->first();
        if(!$admin){
            User::create([
                'foto_user' => 'default.jpg',
                'username' => 'Admin1234',
                'password' =>  bcrypt('Admin1234'),
            ]);
        }
        Cookie::queue(Cookie::forget('remember_token'));

    }

    private function autoLogin()
    {
        try {
            $token = decrypt(Cookie::get('remember_token'));
            [$username, $expires] = explode('|', $token);
            $expiresAt = Carbon::parse($expires);

            $user = User::where('username', $username)->first();

            if ($user && $expiresAt->isFuture() && $user->remember_token_expires && Carbon::now()->lessThan($user->remember_token_expires)) {
                Auth::login($user, true);
                session(['user_id' => $user->id]);
                session()->regenerate();

                redirect()->intended('/dashboard')->send();
                exit;
            }
        } catch (\Exception $e) {
            Cookie::queue(Cookie::forget('remember_token'));
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'password' => 'required|string|min:4'
        ]);

        $username = $request->username;

        // Cek apakah akun terkunci sementara
        if (session("lockout_$username") && Carbon::now()->lessThan(session("lockout_$username"))) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba lagi nanti.');
        }

        $credentials = ['username' => $username, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            session(['user_id' => Auth::user()->id]);
            $request->session()->regenerate();

            // Reset percobaan login jika sukses
            session()->forget("login_attempts_$username");
            session()->forget("lockout_$username");

            // Simpan remember token jika user mencentang "Ingat Saya"
            if ($request->has('remember')) {
                $token = encrypt($username . '|' . now()->addDays(7));
                Cookie::queue('remember_token', $token, 10080); // 7 hari

                User::where('id', Auth::id())->update([
                    'remember_token_expires' => Carbon::now()->addDays(7)
                ]);
            } else {
                Cookie::queue(Cookie::forget('remember_token'));
            }

            return redirect()->intended('/dashboard')->with('berhasil', 'Selamat Datang');
        } else {
            // Tambah jumlah percobaan login gagal
            $attempts = session("login_attempts_$username", 0) + 1;
            session(["login_attempts_$username" => $attempts]);

            // Jika gagal 3 kali, kunci login selama 5 menit
            if ($attempts >= 3) {
                session(["lockout_$username" => Carbon::now()->addMinutes(5)]);
                return redirect()->back()->with('error', 'Terlalu banyak percobaan. Akun dikunci sementara.');
            }

            return redirect()->back()->with('error', 'Username atau password salah.');
        }
    }
}