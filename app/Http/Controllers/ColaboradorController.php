<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public function index() {
        return response()->json(Colaborador::where('activo', true)->get());
    }

    public function store(Request $request) {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellidos'     => 'required|string|max:100',
            'empresa_hotel' => 'required|string|max:100',
        ]);

        $colaborador = Colaborador::create([
            'nombre'        => $request->nombre,
            'apellidos'     => $request->apellidos,
            'empresa_hotel' => $request->empresa_hotel,
            'codigo_qr'     => 'COL-' . strtoupper(substr(md5($request->nombre . $request->apellidos . now()), 0, 8)),
        ]);

        return response()->json($colaborador, 201);
    }
}
