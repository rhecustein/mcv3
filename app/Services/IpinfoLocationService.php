<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IP2LocationService
{
    protected string $endpoint = 'https://api.ip2location.io/';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('IP2LOCATIONIO_API_KEY');
    }

    public function getLocation(string $ip): array
    {
        try {
            $response = Http::get($this->endpoint, [
                'key' => $this->apiKey,
                'ip'  => $ip,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'ip'        => $data['ip'] ?? null,
                    'country'   => $data['country_name'] ?? null,
                    'province'  => $data['region_name'] ?? null,
                    'city'      => $data['city_name'] ?? null,
                    'latitude'  => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ];
            }

            throw new \Exception('Gagal mengambil data lokasi');
        } catch (\Throwable $e) {
            \Log::error('IP2Location Error', ['message' => $e->getMessage()]);
            return [
                'ip'        => $ip,
                'country'   => null,
                'province'  => null,
                'city'      => null,
                'latitude'  => null,
                'longitude' => null,
            ];
        }
    }
}
