<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoreo extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'id_local',
        'programa_id',
        'estacion_id',
        'fecha_hora',
        'monitoreo_fallido',
        'observacion',
        'matriz_id',
        'equipo_multi_id',
        'turbidimetro_id',
        'metodo_id',
        'hidroquimico',
        'isotopico',
        'cod_laboratorio',
        'usuario_id',
        'is_draft',
        'equipo_nivel_id',
        'tipo_pozo',
        'fecha_hora_nivel',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_hora_nivel' => 'datetime',
        'monitoreo_fallido' => 'boolean',
        'hidroquimico' => 'boolean',
        'isotopico' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function detalles()
    {
        return $this->hasMany(MonitoreoDetalle::class);
    }

    public function fotos()
    {
        return $this->hasMany(MonitoreoFoto::class);
    }
}
