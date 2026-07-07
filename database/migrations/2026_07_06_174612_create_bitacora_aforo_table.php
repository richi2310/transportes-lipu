<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitacora_aforo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_id')->constrained('users');
            $table->foreignId('unidad_id')->constrained('unidades');
            $table->foreignId('ruta_id')->constrained('rutas');
            $table->foreignId('colaborador_id')->constrained('colaboradores');
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_aforo');
    }
};
