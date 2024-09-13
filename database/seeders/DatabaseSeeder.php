<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'user_catalogue_id' => 4,
            'name' => 'Trường no 1',
            'email' => 'truongno1@gmail.com',
            'password' => '0123456'
        ]);
    }
}
