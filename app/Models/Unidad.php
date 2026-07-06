<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';  

    protected $fillable = [
        'numero_unidad', 
        'placa', 
        'modelo', 
        'ultimo_km', 
        'activo',
    ];

    public function bitacoraUnidades() {
        return $this->hasMany(BitacoraUnidad::class);
    }
}
