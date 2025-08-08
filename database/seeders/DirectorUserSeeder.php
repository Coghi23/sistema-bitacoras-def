<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DirectorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $director = User::firstOrCreate([
            'email' => 'director@sistema.com'
        ], [
            'name' => 'Director Sistema',
            'cedula' => '12345678',
            'password' => Hash::make('director123'),
            'condicion' => true,
        ]);

        // Asignar rol de director
        $director->assignRole('director');
    }
}
