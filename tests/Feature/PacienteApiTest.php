<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\TipoDocumento;
use App\Models\Genero;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PacienteApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $usuario;
    protected TipoDocumento $tipoDocumento;
    protected Genero $genero;
    protected Departamento $departamento;
    protected Municipio $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        // Creamos un usuario para poder autenticarnos en las rutas protegidas
        $this->usuario = User::factory()->create();

        // Creamos los datos de catálogo mínimos que un paciente necesita
        $this->tipoDocumento = TipoDocumento::create(['nombre' => 'Cédula de Ciudadanía']);
        $this->genero = Genero::create(['nombre' => 'Masculino']);
        $this->departamento = Departamento::create(['nombre' => 'Tolima']);
        $this->municipio = Municipio::create([
            'departamento_id' => $this->departamento->id,
            'nombre' => 'Ibagué',
        ]);
    }

    /** @test */
    #[Test]
    public function puede_crear_un_paciente_con_datos_validos()
    {
        $datos = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'numero_documento' => '1234567890',
            'nombre1' => 'Carlos',
            'nombre2' => null,
            'apellido1' => 'Pérez',
            'apellido2' => null,
            'genero_id' => $this->genero->id,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'correo' => 'carlos.perez@example.com',
        ];

        $respuesta = $this->actingAs($this->usuario)
            ->postJson('/pacientes', $datos);

        $respuesta->assertStatus(201)
            ->assertJson(['message' => 'Paciente creado correctamente']);

        $this->assertDatabaseHas('pacientes', [
            'numero_documento' => '1234567890',
            'correo' => 'carlos.perez@example.com',
        ]);
    }

    /** @test */
    #[Test]
    public function rechaza_la_creacion_si_el_correo_es_invalido()
    {
        $datos = [
            'tipo_documento_id' => $this->tipoDocumento->id,
            'numero_documento' => '1234567891',
            'nombre1' => 'Ana',
            'apellido1' => 'Gómez',
            'genero_id' => $this->genero->id,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'correo' => 'esto-no-es-un-correo',
        ];

        $respuesta = $this->actingAs($this->usuario)
            ->postJson('/pacientes', $datos);

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors(['correo']);

        $this->assertDatabaseMissing('pacientes', [
            'numero_documento' => '1234567891',
        ]);
    }

    /** @test */
    #[Test]
    public function rechaza_la_creacion_si_faltan_campos_obligatorios()
    {
        $respuesta = $this->actingAs($this->usuario)
            ->postJson('/pacientes', []);

        $respuesta->assertStatus(422)
            ->assertJsonValidationErrors([
                'tipo_documento_id', 'numero_documento', 'nombre1',
                'apellido1', 'genero_id', 'departamento_id',
                'municipio_id', 'correo',
            ]);
    }

    /** @test */
    #[Test]
    public function puede_listar_pacientes()
    {
        Paciente::create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'numero_documento' => '1111111111',
            'nombre1' => 'Laura',
            'apellido1' => 'Ramírez',
            'genero_id' => $this->genero->id,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'correo' => 'laura@example.com',
        ]);

        $respuesta = $this->actingAs($this->usuario)
            ->getJson('/pacientes');

        $respuesta->assertStatus(200)
            ->assertJsonFragment(['numero_documento' => '1111111111']);
    }

    /** @test */
    #[Test]
    public function puede_actualizar_un_paciente()
    {
        $paciente = Paciente::create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'numero_documento' => '2222222222',
            'nombre1' => 'Pedro',
            'apellido1' => 'López',
            'genero_id' => $this->genero->id,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'correo' => 'pedro@example.com',
        ]);

        $respuesta = $this->actingAs($this->usuario)
            ->putJson("/pacientes/{$paciente->id}", [
                'tipo_documento_id' => $this->tipoDocumento->id,
                'numero_documento' => '2222222222',
                'nombre1' => 'Pedro',
                'apellido1' => 'López Actualizado',
                'genero_id' => $this->genero->id,
                'departamento_id' => $this->departamento->id,
                'municipio_id' => $this->municipio->id,
                'correo' => 'pedro@example.com',
            ]);

        $respuesta->assertStatus(200)
            ->assertJson(['message' => 'Paciente actualizado correctamente']);

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'apellido1' => 'López Actualizado',
        ]);
    }

    /** @test */
    #[Test]
    public function puede_eliminar_un_paciente()
    {
        $paciente = Paciente::create([
            'tipo_documento_id' => $this->tipoDocumento->id,
            'numero_documento' => '3333333333',
            'nombre1' => 'Sofía',
            'apellido1' => 'Torres',
            'genero_id' => $this->genero->id,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'correo' => 'sofia@example.com',
        ]);

        $respuesta = $this->actingAs($this->usuario)
            ->deleteJson("/pacientes/{$paciente->id}");

        $respuesta->assertStatus(200)
            ->assertJson(['message' => 'Paciente eliminado correctamente']);

        $this->assertDatabaseMissing('pacientes', ['id' => $paciente->id]);
    }

    /** @test */
    #[Test]
    public function un_usuario_no_autenticado_no_puede_acceder_a_pacientes()
    {
        $respuesta = $this->getJson('/pacientes');

        $respuesta->assertStatus(401);
    }
}
