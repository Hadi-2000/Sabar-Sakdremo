<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtangPiutang extends Model
{
    /** @use HasFactory<\Database\Factories\UtangPiutangFactory> */
    use HasFactory;

    protected $table = 'utang_piutangs';

    protected $fillable = ['id_pelanggan','nama','alamat','keterangan','jenis','ambil','nominal','status'];
}
