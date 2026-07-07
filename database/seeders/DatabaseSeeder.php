<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Unidad;
use App\Models\Colaborador;
use App\Models\Ruta;
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
            'name'     => 'Despachador Prueba',
            'email'    => 'despachador@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'despachador',
        ]);
        User::create([
            'name'     => 'Operador Prueba',
            'email'    => 'operador@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'operador',
        ]);
        User::create([
            'name'     => 'Jefe Administracion',
            'email'    => 'jefe.admin@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'jefe_admin',
        ]);
        User::create([
            'name'     => 'Jefe Diesel',
            'email'    => 'jefe.diesel@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'jefe_diesel',
        ]);
        User::create([
            'name'     => 'Gerente Operaciones',
            'email'    => 'gerente@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'gerente_ops',
        ]);
        User::create([
            'name'     => 'Admin Sistema',
            'email'    => 'admin@lipu.com',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
        ]);

        // Unidades de prueba
        foreach (['U-001','U-002','U-003','U-004','U-005'] as $numero) {
            Unidad::create(['numero_unidad' => $numero, 'placa' => 'ABC-'.substr($numero,-3),
                'modelo' => 'Autobús 2022', 'ultimo_km' => rand(10000, 50000)]);
        }

        //Rutas
        Ruta::create(['nombre_ruta' => 'RUTA-01', 'hotel_destino' => 'Hotel Marriott']);
        Ruta::create(['nombre_ruta' => 'RUTA-02', 'hotel_destino' => 'Hotel Hyatt']);
        Ruta::create(['nombre_ruta' => 'RUTA-03', 'hotel_destino' => 'Hotel Hilton']);
    
         // Colaboradores con QR
        $colaboradores = [
            ['Carlos', 'López Pérez', 'Hotel Marriott'],
            ['María', 'González Ruiz', 'Hotel Marriott'],
            ['José', 'Hernández Cruz', 'Hotel Hyatt'],
            ['Ana', 'Martínez Díaz', 'Hotel Hyatt'],
            ['Luis', 'Ramírez Torres', 'Hotel Hilton'],
        ];

        foreach ($colaboradores as [$nombre, $apellidos, $hotel]) {
            Colaborador::create([
                'nombre'       => $nombre,
                'apellidos'    => $apellidos,
                'empresa_hotel'=> $hotel,
                'codigo_qr'    => 'COL-' . strtoupper(substr(md5($nombre.$apellidos), 0, 8)),
            ]);
        }
    }
}
