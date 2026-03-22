<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoreo_id',
        'parameter_id',
        'value',
    ];

    public function monitoreo()
    {
        return $this->belongsTo(Monitoreo::class);
    }
}
