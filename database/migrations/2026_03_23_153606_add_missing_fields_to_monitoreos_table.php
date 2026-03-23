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
        Schema::table('monitoreos', function (Blueprint $table) {
            $table->double('temperatura')->nullable();
            $table->double('ph')->nullable();
            $table->double('conductividad')->nullable();
            $table->double('oxigeno')->nullable();
            $table->double('turbiedad')->nullable();
            $table->double('profundidad')->nullable();
            $table->double('nivel')->nullable();
            $table->double('latitud', 10, 8)->nullable();
            $table->double('longitud', 11, 8)->nullable();
            $table->text('foto_path')->nullable();
            $table->text('foto_multiparametro')->nullable();
            $table->text('foto_turbiedad')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoreos', function (Blueprint $table) {
            $table->dropColumn([
                'temperatura', 'ph', 'conductividad', 'oxigeno', 'turbiedad',
                'profundidad', 'nivel', 'latitud', 'longitud',
                'foto_path', 'foto_multiparametro', 'foto_turbiedad'
            ]);

        });
    }
};
