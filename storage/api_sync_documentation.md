# Sync Monitoreos Documentation

## Overview
The mobile app should use the `POST /api/sync/monitoreos` endpoint to synchronize local SQLite `monitoreos` and related data to the server. The server expects an array of `monitoreos`. It will iterate through the list, decode any provided base64 photos, insert the `monitoreo` item along with its parameters (detalles), and save the image paths.

## Endpoint
**POST** `/api/sync/monitoreos`

### Headers
* `Content-Type: application/json`
* `Accept: application/json`

---

## Example Request

```json
{
    "monitoreos": [
        {
            "id": 1,
            "device_id": "device-uuid-123",
            "programa_id": 5,
            "estacion_id": 10,
            "fecha_hora": "2023-10-15 14:30:00",
            "monitoreo_fallido": false,
            "observacion": "Todo normal",
            "matriz_id": 2,
            "equipo_multi_id": 3,
            "turbidimetro_id": 1,
            "metodo_id": 4,
            "hidroquimico": true,
            "isotopico": false,
            "cod_laboratorio": "LAB-1234",
            "usuario_id": 1,
            "is_draft": false,
            "equipo_nivel_id": null,
            "tipo_pozo": "Profundo",
            "fecha_hora_nivel": "2023-10-15 14:00:00",
            
            "parameters": [
                {
                    "parameter_id": "pH",
                    "value": "7.4"
                },
                {
                    "parameter_id": "Temperature",
                    "value": "22.5"
                }
            ],
            
            "foto_path": "base64_string_here_or_null",
            "foto_multiparametro": "base64_string_here_or_null",
            "foto_turbiedad": "base64_string_here_or_null"
        },
        {
            "id": 2,
            "device_id": "device-uuid-123",
            "programa_id": 5,
            "estacion_id": 11,
            "fecha_hora": "2023-10-15 16:00:00",
            "monitoreo_fallido": true,
            "observacion": "Fallo en medición",
            "parameters": []
        }
    ]
}
```

---

## Example Response

```json
{
    "status": "success",
    "message": "Sync process completed",
    "data": {
        "synced_ids": [
            1,
            2
        ],
        "failed_records": []
    }
}
```

### Characteristics

1. **Idempotency**: The server checks against uniqueness using `id_local` (`id` in the API payload) and `device_id`. If a duplicate is received, it is skipped and treated as "synced" preventing double insertion.
2. **Partial Success**: The server handles records iteratively. If one record fails to insert (due to missing data, etc.), it rolls back *only* that record and appends it to the `failed_records` array. Successfully synced records will appear in `synced_ids`. The app should delete or mark as synced the local records present in `synced_ids`.
3. **Images**: Photos must be included inside the top-level of each `monitoreo` object as `foto_path`, `foto_multiparametro`, or `foto_turbiedad`. They will be decoded and saved in Laravel's `storage/app/public/...` mapped to `monitoreo_fotos`.
