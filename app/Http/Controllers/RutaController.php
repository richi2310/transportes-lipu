<?php

namespace App\Http\Controllers;

use App\Models\Ruta;

class RutaController extends Controller
{
     public function index() {
        return response()->json(Ruta::where('activo', true)->get());
    }
}
