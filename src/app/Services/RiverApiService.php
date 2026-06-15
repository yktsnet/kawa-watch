<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RiverApiService
{
    /**
     * Get the latest water level for a given station.
     *
     * @param  string  $stationCode  The code of the station.
     * @return array|null An array with 'level_m' and 'observed_at' keys, or null on failure.
     */
    public function getLatestWaterLevel(string $stationCode): ?array
    {
        try {
            // Note: Since MLIT API details for real-time water levels aren't specified,
            // we'll implement a robust structure that points to a dummy/placeholder endpoint
            // for MLIT/River Disaster Prevention Information.
            // In a real scenario, this would hit the actual public JSON API endpoint.
            $endpoint = config('services.river_api.endpoint', 'https://www.river.go.jp/kawabou/api/water_level');

            // To simulate actual HTTP call logic without relying on an external API that might be down
            // we use the Http facade. If it's not configured, we'll gracefully fallback or error.
            $response = Http::timeout(5)->get($endpoint, [
                'stationCode' => $stationCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Assuming the API returns JSON with 'waterLevel' and 'observationTime'
                if (isset($data['waterLevel']) && isset($data['observationTime'])) {
                    return [
                        'level_m' => (float) $data['waterLevel'],
                        'observed_at' => $data['observationTime'],
                    ];
                }
            }

            Log::warning("River API failed for station {$stationCode}. Status: ".$response->status());

            return null;

        } catch (\Exception $e) {
            Log::error("Exception in RiverApiService for station {$stationCode}: ".$e->getMessage());

            return null;
        }
    }
}
