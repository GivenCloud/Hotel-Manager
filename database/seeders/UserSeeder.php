<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
            DB::statement('TRUNCATE TABLE users RESTART IDENTITY CASCADE;');
        } else {
            User::truncate();
        }
        
        if (DB::getDriverName() !== 'pgsql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        User::create(
            ['name' => 'Admin',
            'role' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),]
        );

        User::create(
            ['name' => 'Usuario',
            'role' => 'user',
            'email' => 'usuario@gmail.com',
            'password' => bcrypt('123456789'),]
        );
    }
}
