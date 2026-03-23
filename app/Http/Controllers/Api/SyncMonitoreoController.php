<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitoreo;
use App\Models\MonitoreoDetalle;
use App\Models\MonitoreoFoto;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMonitoreoController extends Controller
{
    protected $imageService;

    public function __construct(ImageStorageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Batch sync monitoreos from mobile app to server.
     */
    public function sync(Request $request)
    {
        $validatedData = $request->validate([
            'monitoreos' => 'required|array',
            'monitoreos.*.id' => 'required|integer', // SQLite id_local
            'monitoreos.*.device_id' => 'required|string',
            // other fields are optional or nullable
        ]);

        $monitoreos = $request->input('monitoreos');

        $successIds = [];
        $failedRecords = [];

        foreach ($monitoreos as $data) {
            $idLocal = $data['id'] ?? null;
            $deviceId = $data['device_id'] ?? null;

            if (!$idLocal || !$deviceId) {
                continue;
            }

            try {
                // Determine existence to prevent duplicates
                $exists = Monitoreo::where('id_local', $idLocal)
                    ->where('device_id', $deviceId)
                    ->exists();

                if ($exists) {
                    // Skip or simply report as success since it's already there
                    $successIds[] = $idLocal;
                    continue;
                }

                // Database Transaction per record to guarantee partial success batching
                DB::beginTransaction();

                // Convert SQLite text datetime to real datetime
                $fechaHora = isset($data['fecha_hora']) ? date('Y-m-d H:i:s', strtotime($data['fecha_hora'])) : null;
                $fechaHoraNivel = isset($data['fecha_hora_nivel']) ? date('Y-m-d H:i:s', strtotime($data['fecha_hora_nivel'])) : null;

                // Create Monitoreo directly
                $monitoreo = Monitoreo::create([
                    'device_id' => $deviceId,
                    'id_local' => $idLocal,
                    'programa_id' => $data['programa_id'] ?? null,
                    'estacion_id' => $data['estacion_id'] ?? null,
                    'fecha_hora' => $fechaHora,
                    'monitoreo_fallido' => filter_var($data['monitoreo_fallido'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'observacion' => $data['observacion'] ?? null,
                    'matriz_id' => $data['matriz_id'] ?? null,
                    'equipo_multi_id' => $data['equipo_multi_id'] ?? null,
                    'turbidimetro_id' => $data['turbidimetro_id'] ?? null,
                    'metodo_id' => $data['metodo_id'] ?? null,
                    'hidroquimico' => filter_var($data['hidroquimico'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'isotopico' => filter_var($data['isotopico'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'cod_laboratorio' => $data['cod_laboratorio'] ?? null,
                    'usuario_id' => $data['usuario_id'] ?? null,
                    'is_draft' => filter_var($data['is_draft'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'equipo_nivel_id' => $data['equipo_nivel_id'] ?? null,
                    'tipo_pozo' => $data['tipo_pozo'] ?? null,
                    'fecha_hora_nivel' => $fechaHoraNivel,
                    'temperatura' => $data['temperatura'] ?? null,
                    'ph' => $data['ph'] ?? null,
                    'conductividad' => $data['conductividad'] ?? null,
                    'oxigeno' => $data['oxigeno'] ?? null,
                    'turbiedad' => $data['turbiedad'] ?? null,
                    'profundidad' => $data['profundidad'] ?? null,
                    'nivel' => $data['nivel'] ?? null,
                    'latitud' => $data['latitud'] ?? null,
                    'longitud' => $data['longitud'] ?? null,
                ]);

                // 2. Insert Parameters (Detalles) batch
                if (!empty($data['parameters']) && is_array($data['parameters'])) {
                    $detallesData = [];
                    foreach ($data['parameters'] as $param) {
                        $detallesData[] = [
                            'monitoreo_id' => $monitoreo->id,
                            'parameter_id' => $param['parameter_id'],
                            'value' => $param['value'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (count($detallesData) > 0) {
                        MonitoreoDetalle::insert($detallesData);
                    }
                }

                // 3. Process Images
                $fotosToInsert = [];
                // Original field: foto_path (let's assume this was the general photo)
                if (!empty($data['foto_path'])) {
                    $path = $this->imageService->storeBase64Image($data['foto_path'], $monitoreo, 'general');
                    if ($path) {
                        $monitoreo->foto_path = $path;
                        $fotosToInsert[] = [
                            'monitoreo_id' => $monitoreo->id,
                            'tipo' => 'general',
                            'ruta' => $path,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($data['foto_multiparametro'])) {
                    $path = $this->imageService->storeBase64Image($data['foto_multiparametro'], $monitoreo, 'multiparametro');
                    if ($path) {
                        $monitoreo->foto_multiparametro = $path;
                        $fotosToInsert[] = [
                            'monitoreo_id' => $monitoreo->id,
                            'tipo' => 'multiparametro',
                            'ruta' => $path,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($data['foto_turbiedad'])) {
                    $path = $this->imageService->storeBase64Image($data['foto_turbiedad'], $monitoreo, 'turbiedad');
                    if ($path) {
                        $monitoreo->foto_turbiedad = $path;
                        $fotosToInsert[] = [
                            'monitoreo_id' => $monitoreo->id,
                            'tipo' => 'turbiedad',
                            'ruta' => $path,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (count($fotosToInsert) > 0) {
                    MonitoreoFoto::insert($fotosToInsert);
                    $monitoreo->save();
                }

                DB::commit();
                $successIds[] = $idLocal;

                Log::info("Monitoreo sync successful: local_id {$idLocal}, device {$deviceId}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to sync monitoreo local_id {$idLocal}: " . $e->getMessage());
                
                $failedRecords[] = [
                    'id_local' => $idLocal,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Sync process completed',
            'data' => [
                'synced_ids' => $successIds,   // Array of local IDs that were saved successfully
                'failed_records' => $failedRecords // Array of details for failed records
            ]
        ], 200);
    }
}
