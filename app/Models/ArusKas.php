<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArusKas extends Model
{
    /** @use HasFactory<\Database\Factories\ArusKasFactory> */
    use HasFactory;

    protected $table = 'arus_kas';

    protected $fillable = ['tanggal', 'jenis_kas','jenis_transaksi','jumlah','keterangan'];
}
