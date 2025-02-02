<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        User::create([
            'name' => 'Rifal Kurniawan',
            'email' => 'rifal@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('123123123'),
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'role' => 'user',
            'password' => bcrypt('123123123'),
        ]);
    }
    
}
