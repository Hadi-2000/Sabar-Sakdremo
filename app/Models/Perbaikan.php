<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    /** @use HasFactory<\Database\Factories\PerbaikanFactory> */
    use HasFactory;

    protected $table = 'perbaikans';
    protected $fillable = [
        'id_mesin',
        'teknisi',
        'keterangan',
        'biaya',
        'status'
    ];
}
