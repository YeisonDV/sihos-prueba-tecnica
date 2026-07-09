<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departamentos')->insert([
            ['nombre' => 'Tolima'],
            ['nombre' => 'Cundinamarca'],
            ['nombre' => 'Antioquia'],
            ['nombre' => 'Valle del Cauca'],
            ['nombre' => 'Atlántico'],
        ]);
    }
}
