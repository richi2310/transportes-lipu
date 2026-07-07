<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraDiesel extends Model
{
    protected $table = 'bitacora_diesel';

    protected $fillable = [
        'unidad_id', 'despachador_id', 'km_anterior',
        'km_actual', 'litros_cargados', 'rendimiento_km_litro', 'fecha_hora',
    ];

    public function unidad() {
        return $this->belongsTo(Unidad::class);
    }

    public function despachador() {
        return $this->belongsTo(User::class, 'despachador_id');
    }
}
