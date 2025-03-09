<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenitipanBarang extends Model
{
    /** @use HasFactory<\Database\Factories\PenitipanBarangFactory> */
    use HasFactory;

    protected $table = 'penitipan_barangs';

    protected $fillable = ['id_pelanggan','barang','jumlah','status'];
}
