<?php
namespace App\Http\Controllers;

use App\Models\BitacoraUnidad;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BitacoraUnidadController extends Controller {
    public function index(Request $request) {
        $query = BitacoraUnidad::with(['unidad', 'guardia'])
                    ->orderBy('fecha_hora', 'desc');

        if ($request->has('fecha')) {
            $query->whereDate('fecha_hora', $request->fecha);
        }
        if ($request->has('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request) {
        $request->validate([
            'numero_unidad' => 'required|string',
            'tipo'          => 'required|in:entrada,salida',
            'km_actual'     => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $unidad = Unidad::where('numero_unidad', $request->numero_unidad)
                        ->where('activo', true)
                        ->first();

        if (!$unidad) {
            return response()->json(['message' => 'Unidad no encontrada'], 404);
        }

        if ($request->km_actual < $unidad->ultimo_km) {
            return response()->json([
                'message' => 'El km actual (' . $request->km_actual . ') no puede ser menor al km anterior (' . $unidad->ultimo_km . ')'
            ], 422);
        }

        $registro = BitacoraUnidad::create([
            'unidad_id'     => $unidad->id,
            'guardia_id'    => Auth::id(),
            'tipo'          => $request->tipo,
            'km_anterior'   => $unidad->ultimo_km,
            'km_actual'     => $request->km_actual,
            'observaciones' => $request->observaciones,
            'fecha_hora'    => now(),
        ]);

        $unidad->update(['ultimo_km' => $request->km_actual]);

        return response()->json([
            'message'  => 'Registro guardado correctamente',
            'registro' => $registro->load(['unidad', 'guardia']),
        ], 201);
    }
}