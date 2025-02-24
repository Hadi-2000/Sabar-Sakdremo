<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    /** @use HasFactory<\Database\Factories\KasFactory> */
    use HasFactory;

    protected $fillable = ['jenis_kas', 'saldo'];
}
