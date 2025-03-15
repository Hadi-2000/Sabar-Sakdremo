<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perbaikan>
 */
class PerbaikanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_mesin' => '1',
            'teknisi' => $this->faker->name(),
            'keterangan' => $this->faker->sentence(),
            'biaya' => $this->faker->randomFloat(2, 100000, 5000000),
            'status' => $this->faker->randomElement(['pending','proses','selesai']),
        ];
    }
}
