<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JmaApiService
{
    /**
     * Get the latest weather data for a given station.
     *
     * @param  string  $stationCode  The code of the station or nearest AMeDAS station.
     * @return array|null An array with 'temperature_c', 'precipitation_mm', and 'observed_at' keys, or null on failure.
     */
    public function getLatestWeather(string $stationCode): ?array
    {
        try {
            // JMA (Japan Meteorological Agency) API endpoint for AMeDAS data.
            // Example real endpoint: https://www.jma.go.jp/bosai/amedas/data/map/YYYYMMDDHH0000.json
            // We use a configured endpoint or a placeholder.
            $endpoint = config('services.jma_api.endpoint', 'https://www.jma.go.jp/bosai/amedas/data/latest_time.txt');

            // First, get the latest time from JMA API (standard pattern for JMA AMeDAS)
            $timeResponse = Http::timeout(5)->get($endpoint);

            if ($timeResponse->successful()) {
                $latestTime = trim($timeResponse->body()); // e.g. "2023-10-25T12:00:00+09:00"
                $formattedTime = date('YmdHi00', strtotime($latestTime));

                $dataEndpoint = "https://www.jma.go.jp/bosai/amedas/data/map/{$formattedTime}.json";
                $dataResponse = Http::timeout(5)->get($dataEndpoint);

                if ($dataResponse->successful()) {
                    $data = $dataResponse->json();

                    // The JMA API uses station codes as keys in the JSON response
                    // Here we assume $stationCode maps to the AMeDAS code.
                    if (isset($data[$stationCode])) {
                        $stationData = $data[$stationCode];

                        return [
                            'temperature_c' => isset($stationData['temp'][0]) ? (float) $stationData['temp'][0] : null,
                            'precipitation_mm' => isset($stationData['precipitation1h'][0]) ? (float) $stationData['precipitation1h'][0] : null,
                            'observed_at' => date('Y-m-d H:i:s', strtotime($latestTime)),
                        ];
                    }
                }
            }

            Log::warning("JMA API failed or missing data for station {$stationCode}.");

            return null;

        } catch (\Exception $e) {
            Log::error("Exception in JmaApiService for station {$stationCode}: ".$e->getMessage());

            return null;
        }
    }
}
