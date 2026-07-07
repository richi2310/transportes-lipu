<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAforo;
use App\Models\Colaborador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AforoController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'codigo_qr' => 'required|string',
            'unidad_id' => 'required|exists:unidades,id',
            'ruta_id'   => 'required|exists:rutas,id',
        ]);

        $colaborador = Colaborador::where('codigo_qr', $request->codigo_qr)
                                  ->where('activo', true)->first();

        if (!$colaborador) {
            return response()->json(['message' => 'Código QR no reconocido'], 404);
        }

        $yaRegistrado = BitacoraAforo::where('colaborador_id', $colaborador->id)
        ->where('unidad_id', $request->unidad_id)
        ->where('ruta_id', $request->ruta_id)
        ->whereDate('fecha_hora', now()->toDateString())
        ->exists();
        
        if ($yaRegistrado) {
            return response()->json([
                'message' => '⚠ ' . $colaborador->nombre . ' ' . $colaborador->apellidos . ' ya fue registrado hoy en esta ruta'
            ], 409);
        }

        $registro = BitacoraAforo::create([
            'operador_id'    => Auth::id(),
            'unidad_id'      => $request->unidad_id,
            'ruta_id'        => $request->ruta_id,
            'colaborador_id' => $colaborador->id,
            'fecha_hora'     => now(),
        ]);

        return response()->json([
            'message'     => 'Abordaje registrado',
            'colaborador' => $colaborador->nombre . ' ' . $colaborador->apellidos,
            'hotel'       => $colaborador->empresa_hotel,
            'registro'    => $registro,
        ], 201);
    }

    public function reporteSemanal(Request $request) {
        $inicio = $request->get('inicio', now()->startOfWeek()->toDateString());
        $fin    = $request->get('fin',    now()->endOfWeek()->toDateString());

        $datos = BitacoraAforo::with(['unidad', 'ruta', 'colaborador'])
            ->whereBetween('fecha_hora', [$inicio . ' 00:00:00', $fin . ' 23:59:59'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $resumen = BitacoraAforo::selectRaw('ruta_id, unidad_id, COUNT(*) as total')
            ->whereBetween('fecha_hora', [$inicio . ' 00:00:00', $fin . ' 23:59:59'])
            ->groupBy('ruta_id', 'unidad_id')
            ->with(['ruta', 'unidad'])
            ->get();

        return response()->json([
            'periodo' => ['inicio' => $inicio, 'fin' => $fin],
            'resumen' => $resumen,
            'detalle' => $datos,
        ]);
    }
}
