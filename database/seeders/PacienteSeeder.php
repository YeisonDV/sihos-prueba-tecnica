<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cc = DB::table('tipos_documento')->where('nombre', 'Cédula de Ciudadanía')->value('id');
        $ti = DB::table('tipos_documento')->where('nombre', 'Tarjeta de Identidad')->value('id');

        $masculino = DB::table('genero')->where('nombre', 'Masculino')->value('id');
        $femenino = DB::table('genero')->where('nombre', 'Femenino')->value('id');

        $tolima = DB::table('departamentos')->where('nombre', 'Tolima')->value('id');
        $ibague = DB::table('municipios')->where('nombre', 'Ibagué')->value('id');

        $cundinamarca = DB::table('departamentos')->where('nombre', 'Cundinamarca')->value('id');
        $bogota = DB::table('municipios')->where('nombre', 'Bogotá')->value('id');

        DB::table('pacientes')->insert([
            [
                'tipo_documento_id' => $cc,
                'numero_documento' => '1001234567',
                'nombre1' => 'Yeison',
                'nombre2' => null,
                'apellido1' => 'García',
                'apellido2' => 'López',
                'genero_id' => $masculino,
                'departamento_id' => $tolima,
                'municipio_id' => $ibague,
                'correo' => 'yeison.garcia@example.com',
            ],
            [
                'tipo_documento_id' => $cc,
                'numero_documento' => '1002345678',
                'nombre1' => 'María',
                'nombre2' => 'Fernanda',
                'apellido1' => 'Rodríguez',
                'apellido2' => 'Pérez',
                'genero_id' => $femenino,
                'departamento_id' => $cundinamarca,
                'municipio_id' => $bogota,
                'correo' => 'maria.rodriguez@example.com',
            ],
            [
                'tipo_documento_id' => $ti,
                'numero_documento' => '1003456789',
                'nombre1' => 'Juan',
                'nombre2' => null,
                'apellido1' => 'Martínez',
                'apellido2' => null,
                'genero_id' => $masculino,
                'departamento_id' => $tolima,
                'municipio_id' => $ibague,
                'correo' => 'juan.martinez@example.com',
            ],
            [
                'tipo_documento_id' => $cc,
                'numero_documento' => '1004567890',
                'nombre1' => 'Laura',
                'nombre2' => null,
                'apellido1' => 'Gómez',
                'apellido2' => 'Torres',
                'genero_id' => $femenino,
                'departamento_id' => $cundinamarca,
                'municipio_id' => $bogota,
                'correo' => 'laura.gomez@example.com',
            ],
            [
                'tipo_documento_id' => $ti,
                'numero_documento' => '1005678901',
                'nombre1' => 'Andrés',
                'nombre2' => 'Felipe',
                'apellido1' => 'Ramírez',
                'apellido2' => null,
                'genero_id' => $masculino,
                'departamento_id' => $tolima,
                'municipio_id' => $ibague,
                'correo' => 'andres.ramirez@example.com',
            ],
        ]);
    }
}
