<?php
namespace App\Http\Controllers;

use App\Models\BitacoraPaqueteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class BitacoraPaqueteriaController extends Controller {
    public function index() {
        return response()->json(
            BitacoraPaqueteria::with('guardia')
                ->orderBy('fecha_hora', 'desc')
                ->paginate(20)
        );
    }

    public function store(Request $request) {
        $request->validate([
            'remitente'         => 'required|string|max:100',
            'destinatario'      => 'required|string|max:100',
            'descripcion'       => 'required|string|max:255',
            'cantidad_paquetes' => 'required|integer|min:1',
        ]);

        $registro = BitacoraPaqueteria::create([
            'guardia_id'        => Auth::id(),
            'remitente'         => $request->remitente,
            'destinatario'      => $request->destinatario,
            'descripcion'       => $request->descripcion,
            'cantidad_paquetes' => $request->cantidad_paquetes,
            'fecha_hora'        => now(),
        ]);

        $enviado = false;
        $error   = null;

        try {
            $twilio  = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $mensaje = "LIPU - Paquete recibido:\n" .
                       "De: {$request->remitente}\n" .
                       "Para: {$request->destinatario}\n" .
                       "Desc: {$request->descripcion}\n" .
                       "Cantidad: {$request->cantidad_paquetes}\n" .
                       "Hora: " . now()->format('d/m/Y H:i');

            $twilio->messages->create(
                config('services.twilio.to'),
                ['from' => config('services.twilio.from'), 'body' => $mensaje]
            );
            $enviado = true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $registro->update([
            'notificacion_enviada' => $enviado,
            'error_notificacion'   => $error,
        ]);

        return response()->json([
            'message'              => 'Paquete registrado',
            'notificacion_enviada' => $enviado,
            'registro'             => $registro,
        ], 201);
    }
}