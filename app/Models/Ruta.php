<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
     protected $table = 'rutas';
     
    protected $fillable = [
        'nombre_ruta', 'hotel_destino', 'activo',
    ];

    public function bitacoraAforo() {
        return $this->hasMany(BitacoraAforo::class);
    }
}
