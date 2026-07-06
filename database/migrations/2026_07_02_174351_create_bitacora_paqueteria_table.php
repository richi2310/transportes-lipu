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
        Schema::create('bitacora_paqueteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardia_id')->constrained('users');
            $table->string('remitente');
            $table->string('destinatario');
            $table->string('descripcion');
            $table->integer('cantidad_paquetes')->default(1);
            $table->boolean('notificacion_enviada')->default(false);
            $table->string('error_notificacion')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_paqueteria');
    }
};
