<?php

namespace App\Http\Controllers;

use App\Models\BitacoraDiesel;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BitacoraDieselController extends Controller
{
    public function index(Request $request) {
        $query = BitacoraDiesel::with(['unidad', 'despachador'])
                    ->orderBy('fecha_hora', 'desc');

        if ($request->has('unidad_id')) $query->where('unidad_id', $request->unidad_id);
        if ($request->has('fecha'))     $query->whereDate('fecha_hora', $request->fecha);

        return response()->json($query->paginate(20));
    }

    public function store(Request $request) {
        $request->validate([
            'numero_unidad'   => 'required|string',
            'km_actual'       => 'required|integer|min:0',
            'litros_cargados' => 'required|numeric|min:0.1',
        ]);

        $unidad = Unidad::where('numero_unidad', $request->numero_unidad)
                        ->where('activo', true)->first();

        if (!$unidad) {
            return response()->json(['message' => 'Unidad no encontrada'], 404);
        }

        if ($request->km_actual < $unidad->ultimo_km) {
            return response()->json([
                'message' => 'El km actual (' . $request->km_actual . ') no puede ser menor al anterior (' . $unidad->ultimo_km . ')'
            ], 422);
        }

        $diferencia    = $request->km_actual - $unidad->ultimo_km;
        $rendimiento   = $diferencia > 0 ? round($diferencia / $request->litros_cargados, 2) : null;

        $registro = BitacoraDiesel::create([
            'unidad_id'          => $unidad->id,
            'despachador_id'     => Auth::id(),
            'km_anterior'        => $unidad->ultimo_km,
            'km_actual'          => $request->km_actual,
            'litros_cargados'    => $request->litros_cargados,
            'rendimiento_km_litro' => $rendimiento,
            'fecha_hora'         => now(),
        ]);

        $unidad->update(['ultimo_km' => $request->km_actual]);

        return response()->json([
            'message'   => 'Carga de diésel registrada correctamente',
            'registro'  => $registro->load(['unidad', 'despachador']),
        ], 201);
    }
}
