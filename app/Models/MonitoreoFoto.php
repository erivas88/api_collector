<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoFoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoreo_id',
        'tipo',
        'ruta',
    ];

    public function monitoreo()
    {
        return $this->belongsTo(Monitoreo::class);
    }
}
