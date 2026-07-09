<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Paciente::with(['tipoDocumento', 'genero', 'departamento', 'municipio']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
             $q->where('nombre1', 'like', "%{$buscar}%")
              ->orWhere('nombre2', 'like', "%{$buscar}%")
              ->orWhere('apellido1', 'like', "%{$buscar}%")
              ->orWhere('apellido2', 'like', "%{$buscar}%")
              ->orWhere('correo', 'like', "%{$buscar}%");
        });
    }

    $pacientes = $query->orderBy('id', 'desc')->paginate(5);

    return response()->json($pacientes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'numero_documento'  => 'required|string|max:20|unique:pacientes,numero_documento',
            'nombre1'           => 'required|string|max:100',
            'nombre2'           => 'nullable|string|max:100',
            'apellido1'         => 'required|string|max:100',
            'apellido2'         => 'nullable|string|max:100',
            'genero_id'         => 'required|exists:genero,id',
            'departamento_id'   => 'required|exists:departamentos,id',
            'municipio_id'      => 'required|exists:municipios,id',
            'correo'            => 'required|email|unique:pacientes,correo',
        ]);

        $paciente = Paciente::create($validated);

        return response()->json([
            'message' => 'Paciente creado correctamente',
            'paciente' => $paciente,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paciente = Paciente::with(['tipoDocumento', 'genero', 'departamento', 'municipio'])
            ->findOrFail($id);

        return response()->json($paciente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paciente = Paciente::findOrFail($id);

        $validated = $request->validate([
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'numero_documento'  => 'required|string|max:20|unique:pacientes,numero_documento,' . $paciente->id,
            'nombre1'           => 'required|string|max:100',
            'nombre2'           => 'nullable|string|max:100',
            'apellido1'         => 'required|string|max:100',
            'apellido2'         => 'nullable|string|max:100',
            'genero_id'         => 'required|exists:genero,id',
            'departamento_id'   => 'required|exists:departamentos,id',
            'municipio_id'      => 'required|exists:municipios,id',
            'correo'            => 'required|email|unique:pacientes,correo,' . $paciente->id,
        ]);

        $paciente->update($validated);

        return response()->json([
            'message' => 'Paciente actualizado correctamente',
            'paciente' => $paciente,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        return response()->json([
            'message' => 'Paciente eliminado correctamente',
        ]);
    }
}
