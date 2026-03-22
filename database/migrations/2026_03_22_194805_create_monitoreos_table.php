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
        Schema::create('monitoreos', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->unsignedBigInteger('id_local');
            
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->unsignedBigInteger('estacion_id')->nullable();
            $table->dateTime('fecha_hora')->nullable();
            $table->boolean('monitoreo_fallido')->default(false);
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('matriz_id')->nullable();
            $table->unsignedBigInteger('equipo_multi_id')->nullable();
            $table->unsignedBigInteger('turbidimetro_id')->nullable();
            $table->unsignedBigInteger('metodo_id')->nullable();
            $table->boolean('hidroquimico')->default(false);
            $table->boolean('isotopico')->default(false);
            $table->string('cod_laboratorio')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->boolean('is_draft')->default(false);
            $table->unsignedBigInteger('equipo_nivel_id')->nullable();
            $table->string('tipo_pozo')->nullable();
            $table->dateTime('fecha_hora_nivel')->nullable();
            
            $table->timestamps();

            // Enforce unique constraint combining id_local and device_id
            $table->unique(['id_local', 'device_id'], 'unique_monitoreo_device_local');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoreos');
    }
};
