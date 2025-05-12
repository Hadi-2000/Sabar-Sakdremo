<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /** @use HasFactory<\Database\Factories\PegawaiFactory> */
    use HasFactory;

    protected $table = 'pegawais';

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'status',
        'kehadiran',
        'cek_in',
        'cek_out',
        'gaji',
        'gaji_hari_ini',
        'beban_gaji'
    ];


}
