<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraAforo extends Model
{
    protected $table = 'bitacora_aforo';

    protected $fillable = [
        'operador_id', 'unidad_id', 'ruta_id', 'colaborador_id', 'fecha_hora',
    ];

    public function operador() {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function unidad() {
        return $this->belongsTo(Unidad::class);
    }

    public function ruta() {
        return $this->belongsTo(Ruta::class);
    }

    public function colaborador() {
        return $this->belongsTo(Colaborador::class);
    }
}
