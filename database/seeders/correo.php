<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class correo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mail::raw('Este es un correo de prueba desde el sandbox.', function ($message) {
            $message->to('test@example.com') // Cambia esta dirección por una válida en Mailtrap
                    ->subject('Correo de Prueba');
        });

        $this->command->info('Correo de prueba enviado. Verifica tu inbox en Mailtrap.');
    }
}
