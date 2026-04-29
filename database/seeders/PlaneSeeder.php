<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaneSeeder extends Seeder
{
    public function run()
    {
        DB::table('planes')->insert([
            [
                'nombre'        => 'Trial',
                'precio_euros'  => 0.00,
                'tokens_mes'    => 500000,
                'duracion_dias' => 30,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Basic',
                'precio_euros'  => 19.00,
                'tokens_mes'    => 2000000,
                'duracion_dias' => 30,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Pro',
                'precio_euros'  => 40.00,
                'tokens_mes'    => 8000000,
                'duracion_dias' => 30,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
