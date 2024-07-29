<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Hotel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->city(),
            'stars' => $this->faker->numberBetween(1, 5),
            'phone' => $this->faker->randomNumber(9),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
        ];
    }
}
