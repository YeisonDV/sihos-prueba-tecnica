<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tolima = DB::table('departamentos')->where('nombre', 'Tolima')->value('id');
        $cundinamarca = DB::table('departamentos')->where('nombre', 'Cundinamarca')->value('id');
        $antioquia = DB::table('departamentos')->where('nombre', 'Antioquia')->value('id');
        $valle = DB::table('departamentos')->where('nombre', 'Valle del Cauca')->value('id');
        $atlantico = DB::table('departamentos')->where('nombre', 'Atlántico')->value('id');

        DB::table('municipios')->insert([
            ['departamento_id' => $tolima, 'nombre' => 'Ibagué'],
            ['departamento_id' => $tolima, 'nombre' => 'Espinal'],

            ['departamento_id' => $cundinamarca, 'nombre' => 'Bogotá'],
            ['departamento_id' => $cundinamarca, 'nombre' => 'Soacha'],

            ['departamento_id' => $antioquia, 'nombre' => 'Medellín'],
            ['departamento_id' => $antioquia, 'nombre' => 'Envigado'],

            ['departamento_id' => $valle, 'nombre' => 'Cali'],
            ['departamento_id' => $valle, 'nombre' => 'Palmira'],

            ['departamento_id' => $atlantico, 'nombre' => 'Barranquilla'],
            ['departamento_id' => $atlantico, 'nombre' => 'Soledad'],
        ]);
    }
}
