<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
         //Asegúrate de no crear usuarios duplicados
        User::updateOrCreate(
            ['email' => 'usuario@example.com'],
            [
                'name' => 'Usuario Prueba',
                'password' => bcrypt('password'),
            ]
        );
    }
}