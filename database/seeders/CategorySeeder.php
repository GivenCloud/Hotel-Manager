<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('TRUNCATE TABLE categories RESTART IDENTITY CASCADE;');
        } else {
            Category::truncate();
        }
        
        if (DB::getDriverName() !== 'pgsql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        Category::create(
            ['name' => 'Entertainment']
        );

        Category::create(
            ['name' => 'Food']
        );

        Category::create(
            ['name' => 'Instalation']
        );
    }
}
