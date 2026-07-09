<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\TipoDocumento;
use App\Models\Genero;
use Illuminate\Http\Request;


class CatalogoController extends Controller
{
    public function departamentos()
    {
        return response()->json(Departamento::orderBy('nombre')->get());
    }

    public function tiposDocumento()
    {
        return response()->json(TipoDocumento::orderBy('nombre')->get());
    }

    public function generos()
    {
        return response()->json(Genero::orderBy('nombre')->get());
    }

    public function municipiosPorDepartamento(string $departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)
            ->orderBy('nombre')
            ->get();

        return response()->json($municipios);
    }
}
