<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    /** @use HasFactory<\Database\Factories\AsetFactory> */
    use HasFactory;

    protected $table = 'asets';
    protected $fillable = [
        'nama', 'deskripsi', 'satuan', 'harga_satuan'
    ];
}
