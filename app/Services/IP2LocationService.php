<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IP2LocationService
{
    protected string $endpoint = 'https://api.ip2location.io/';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('IP2LOCATIONIO_API_KEY');

        if (!$this->apiKey) {
            Log::warning('IP2Location API key not set in .env');
        }
    }

    public function getLocation(string $ip): array
    {
        try {
            $response = Http::timeout(5)->get($this->endpoint, [
                'key' => $this->apiKey,
                'ip'  => $ip,
            ]);

            if (!$response->successful()) {
                Log::warning('IP2Location API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->emptyResult($ip);
            }

            $data = $response->json();

            return [
                'ip'        => $data['ip'] ?? $ip,
                'country'   => $data['country_name'] ?? null,
                'province'  => $data['region_name'] ?? null,
                'city'      => $data['city_name'] ?? null,
                'latitude'  => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('IP2Location Error', ['message' => $e->getMessage()]);
            return $this->emptyResult($ip);
        }
    }

    protected function emptyResult(string $ip): array
    {
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
