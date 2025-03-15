<?php

namespace Database\Seeders;

use App\Models\Perbaikan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PerbaikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Perbaikan::factory()->count(10)->create();
    }
}
