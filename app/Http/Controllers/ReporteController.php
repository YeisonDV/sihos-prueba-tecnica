<?php

namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function pacientesPorDepartamento()
    {
        // Tomamos los datos de conexión directamente del .env (mismos que usa Laravel)
        $host = env('DB_HOST', '127.0.0.1');
        $puerto = env('DB_PORT', '3306');
        $baseDatos = env('DB_DATABASE');
        $usuario = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        try {
            // Conexión directa con PDO, sin pasar por Eloquent
            $pdo = new PDO(
                "mysql:host={$host};port={$puerto};dbname={$baseDatos};charset=utf8mb4",
                $usuario,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Consulta preparada (protege contra inyección SQL)
            $sql = "SELECT d.nombre AS departamento, COUNT(p.id) AS total_pacientes
                    FROM departamentos d
                    LEFT JOIN pacientes p ON p.departamento_id = d.id
                    GROUP BY d.id, d.nombre
                    ORDER BY total_pacientes DESC";

            $consulta = $pdo->prepare($sql);
            $consulta->execute();

            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

            return response()->json($resultados);

        } catch (\PDOException $e) {
            return response()->json([
                'message' => 'Error al conectar con la base de datos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}