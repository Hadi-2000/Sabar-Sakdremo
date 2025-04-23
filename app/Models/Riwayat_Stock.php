<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_Stock extends Model
{
    /** @use HasFactory<\Database\Factories\RiwayatStockFactory> */
    use HasFactory;

    protected $table = 'riwayat__stoks';
    protected $fillable = ['product_id','tipe','jumlah','stock_sebelum','stock_setelah','keterangan'];
}
