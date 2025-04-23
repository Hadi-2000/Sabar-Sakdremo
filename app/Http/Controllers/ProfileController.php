<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAsetRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateAsetRequest;

class ProfileController extends Controller
{
    public function __construct()
    {
        if (!app('session')->has('user_id')) {
            redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu')->send();
            exit; // Pastikan eksekusi berhenti di sini
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try { 
            //ambil data dari user dari user yang login
            $user = Auth::user();
            //mengambil data id yang 
            return view('tampilan.profile.profile', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error pada ProfileController@index: ' . $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada server.');
        }
    }

    /**
     * Search for the specified resource.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function editData()
    {
        return view('tampilan.profile.edit-data');
    }
    public function editPassword()
    {
        return view('tampilan.profile.edit-password');
    }
    public function updateData(Request $request)
{
    try {
        DB::beginTransaction();

        // Debugging: Cek data request masuk
        Log::info('Data masuk: ', $request->all());

        // Validasi input
        $data = $request->validate([
            'foto_user' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|numeric',
        ]);

        // Debugging: Cek apakah user terautentikasi
        if (!Auth::check()) {
            throw new \Exception('User tidak terautentikasi');
        }

        $user = Auth::user();
        $filename = $user->foto_user; // Jika tidak ada upload, pakai yang lama

        // Jika ada file gambar yang di-upload
        if ($request->hasFile('foto_user')) {
            $file = $request->file('foto_user');

            // Gunakan md5() agar hasilnya tetap sama
            $filename = time() . '-' . $user->id . '.' . $file->getClientOriginalExtension();

            // Simpan di storage/app/public/images/profile/
            $path = $file->storeAs('images/profile', $filename,'public');

            // Debugging: Cek apakah file berhasil disimpan
            if (!$path) {
                throw new \Exception('Gagal menyimpan file.');
            }

            if ($user->foto_user && Storage::disk('public')->exists('images/profile/' . $user->foto_user)) {
                Log::info('Menghapus foto: ' . $user->foto_user);
                Storage::disk('public')->delete('images/profile/' . $user->foto_user);
            } else {
                Log::info('Foto tidak ditemukan: ' . $user->foto_user);
            }
        }

        // Update data user langsung dengan model user yang sudah ditemukan
        User::find($user->id)->update([
            'foto_user' => $filename,
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'no_hp' => $data['no_hp'],
        ]);

        DB::commit();
        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error pada ProfileController@updateData: ' . $e->getMessage());
        return redirect()->route('profile.edit.data')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
public function updatePassword(Request $request){
    try{
        $data = $request->validate([
            'password_lama' =>'required|string',
            'password_baru' =>'required|string',
        ]);
        $user = Auth::user();
        if(!Hash::check($data['password_lama'], $user->password)){
            return redirect()->route('profile.edit.password')->with('error', 'Password Lama Salah.');
        }
        //cek user password lama dan baru sama
        if($data['password_lama'] == $data['password_baru']){
            return redirect()->route('profile.edit.password')->with('error', 'Password Baru Tidak Boleh Sama Dengan Password Lama.');
        }
        DB::beginTransaction();
            User::find($user->id)->update([
                'password' => Hash::make($data['password_baru'])
            ]);
        DB::commit();
        return redirect()->route('profile.index')->with('success', 'Password Berhasil Diubah.');
    }catch(\Exception $e){
        DB::rollback();
        Log::error('Error pada ProfileController@updatePassword: '. $e->getMessage());
        return redirect()->route('profile.edit.password')->with('error', 'Terjadi kesalahan: '. $e->getMessage());
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAsetRequest $request)
    {
        try{
            $data = $request->validated();
            $jumlah = str_replace('.','',$data['jumlah']);

            DB::beginTransaction();
            
            Aset::create([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'jumlah' => $jumlah,
                'satuan' => $data['satuan'],
                'harga_satuan' =>$data['harga_satuan']
            ]);

            DB::commit();

        return redirect()->route('aset.index')->with('success', 'Data Berhasil Ditambahkan.');
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada AsetController@store: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada server.');
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aset $produk)
    {
        if(!$produk){
            return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
        }
        return view('tampilan.penggilingan.produk.update', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAsetRequest $request,Aset $aset)
    {
        try{
            $data = $request->validated();
            if ($aset === null) {
                return redirect()->route('aset.index')->with('error', 'Data Aset Tidak Ditemukan.');
            }
            $jumlah = str_replace('.','',$data['jumlah']);
            DB::beginTransaction();
            $aset->update([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'jumlah' => $jumlah,
                'satuan' => $data['satuan'],
                'harga_satuan' =>$data['harga_satuan']
            ]);
            DB::commit();
            return redirect()->route('aset.index')->with('success', 'Data Berhasil Diubah.');
        }catch(\Exception $e){
            DB::rollback();
            Log::error('Error pada AsetController@update: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada server.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aset $aset)
    {
        try{
            $aset->delete();
            return redirect()->route('aset.index')->with('success', 'Data Berhasil Dihapus.');
        }catch(\Exception $e){
            Log::error('Error pada AsetController@destroy: '. $e->getMessage());
            return redirect()->route('aset.index')->with('error', 'Terjadi kesalahan pada server.');
        }
    }
}
