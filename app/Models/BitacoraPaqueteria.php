<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraPaqueteria extends Model
{
    protected $table = 'bitacora_paqueteria';  

    protected $fillable = [
        'guardia_id', 
        'remitente', 
        'destinatario',
        'descripcion', 
        'cantidad_paquetes',
        'notificacion_enviada', 
        'error_notificacion', 
        'fecha_hora',
    ];

    public function guardia() {
        return $this->belongsTo(User::class, 'guardia_id');
    }
}
