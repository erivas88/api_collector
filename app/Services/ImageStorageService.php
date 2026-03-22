<?php

namespace App\Services;

use App\Models\Monitoreo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorageService
{
    /**
     * Store a base64 encoded image for a monitoreo.
     * 
     * @param string $base64Image The base64 image data (with or without data URI scheme)
     * @param Monitoreo $monitoreo The associated monitoreo
     * @param string $tipo The type of image (general, multiparametro, turbiedad)
     * @return string|null The relative path where the image was stored, or null if invalid
     */
    public function storeBase64Image(?string $base64Image, Monitoreo $monitoreo, string $tipo): ?string
    {
        if (empty($base64Image)) {
            return null;
        }

        // Handle if base64 contains data URI scheme e.g "data:image/jpeg;base64,..."
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1] == 'jpeg' ? 'jpg' : $matches[1];
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        } else {
            // Default extension if no data URI is provided
            $extension = 'jpg';
        }

        // Decode the image
        $imageData = base64_decode($base64Image);
        if ($imageData === false) {
            return null;
        }

        // Determine directory path: monitoreos/{device_id}/{year}/{month}/monitoreo_{id}/
        $year = $monitoreo->fecha_hora ? $monitoreo->fecha_hora->format('Y') : date('Y');
        $month = $monitoreo->fecha_hora ? $monitoreo->fecha_hora->format('m') : date('m');
        $deviceId = $monitoreo->device_id;
        $monitoreoId = $monitoreo->id;

        $directory = "monitoreos/{$deviceId}/{$year}/{$month}/monitoreo_{$monitoreoId}";
        
        // Generate unique filename
        $filename = "{$tipo}_" . Str::random(10) . ".{$extension}";
        $path = "{$directory}/{$filename}";

        // Store the file in the public disk
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}
