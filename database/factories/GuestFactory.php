<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (DB::getDriverName() !== 'pgsql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('TRUNCATE TABLE guests RESTART IDENTITY CASCADE;');
        } else {
            Guest::truncate();
        }
        
        if (DB::getDriverName() !== 'pgsql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        return [
            'name' => $this->faker->firstName(),
            'lastName' => $this->faker->lastName(),
            'dniPassport' => $this->faker->randomNumber(8).$this->faker->toUpper($this->faker->randomLetter()),
            'email' => $this->faker->email(),
            'phone' => $this->faker->randomNumber(9),
        ];
    }
}
