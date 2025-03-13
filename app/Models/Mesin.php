<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    /** @use HasFactory<\Database\Factories\MesinFactory> */
    use HasFactory;

    protected $table = 'mesins';
    protected $fillable = ['nama_mesin','merek_mesin'];
}
