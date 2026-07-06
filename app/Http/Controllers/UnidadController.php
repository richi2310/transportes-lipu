<?php
namespace App\Http\Controllers;

use App\Models\Unidad;

class UnidadController extends Controller {
    public function buscarPorNumero($numero) {
        $unidad = Unidad::where('numero_unidad', $numero)
                        ->where('activo', true)
                        ->first();

        if (!$unidad) {
            return response()->json([
                'message' => 'Unidad no encontrada'
            ], 404);
        }

        return response()->json([
            'id'            => $unidad->id,
            'numero_unidad' => $unidad->numero_unidad,
            'placa'         => $unidad->placa,
            'modelo'        => $unidad->modelo,
            'ultimo_km'     => $unidad->ultimo_km,
        ]);
    }
}