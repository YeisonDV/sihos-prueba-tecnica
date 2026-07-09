<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@sihos.com',
            'password' => bcrypt('1234567890'),
        ]);

        $this->call([
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            TipoDocumentoSeeder::class,
            GeneroSeeder::class,
            PacienteSeeder::class,
        ]);
    }
}
