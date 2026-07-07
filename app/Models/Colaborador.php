<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    protected $table = 'colaboradores';
    
    protected $fillable = [
        'nombre', 'apellidos', 'empresa_hotel', 'codigo_qr', 'activo',
    ];

    public function bitacoraAforo() {
        return $this->hasMany(BitacoraAforo::class);
    }
}
