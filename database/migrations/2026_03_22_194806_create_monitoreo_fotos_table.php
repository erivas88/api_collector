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
        Schema::create('monitoreo_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoreo_id')->constrained('monitoreos')->cascadeOnDelete();
            $table->string('tipo')->comment('general, multiparametro, turbiedad');
            $table->string('ruta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoreo_fotos');
    }
};
