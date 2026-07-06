<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraUnidad extends Model
{
    protected $table = 'bitacora_unidades';  

    protected $fillable = [
        'unidad_id', 
        'guardia_id', 
        'tipo',
        'km_anterior', 
        'km_actual', 
        'observaciones', 
        'fecha_hora',
    ];

    public function unidad() {
        return $this->belongsTo(Unidad::class);
    }

    public function guardia() {
        return $this->belongsTo(User::class, 'guardia_id');
    }
}
