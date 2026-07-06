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
        Schema::create('bitacora_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_id')->constrained('unidades');
            $table->foreignId('guardia_id')->constrained('users');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->integer('km_anterior');
            $table->integer('km_actual');
            $table->integer('diferencia_km')->storedAs('km_actual - km_anterior');
            $table->string('observaciones')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_unidades');
    }
};
