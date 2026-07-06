<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Unidad;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    User::create([
            'name'     => 'Guardia Prueba',
            'email'    => 'guardia@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'guardia',
        ]);
        User::create([
            'name'     => 'Jefe Administracion',
            'email'    => 'jefe.admin@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'jefe_admin',
        ]);
        User::create([
            'name'     => 'Admin Sistema',
            'email'    => 'admin@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
        ]);

        // Unidades de prueba
        $unidades = ['U-001', 'U-002', 'U-003', 'U-004', 'U-005'];
        foreach ($unidades as $numero) {
            Unidad::create([
                'numero_unidad' => $numero,
                'placa'         => 'ABC-' . substr($numero, -3),
                'modelo'        => 'Autobús 2022',
                'ultimo_km'     => rand(10000, 50000),
            ]);
        }
    }
}
