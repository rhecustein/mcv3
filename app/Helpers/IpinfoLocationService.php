<?php

namespace App\Services;

use ipinfo\ipinfo\IPinfo;

class IpinfoLocationService
{
    protected IPinfo $client;

    public function __construct()
    {
        $token = config('services.ipinfo.token');
        $this->client = new IPinfo($token);
    }

    public function getLocation(string $ip): array
    {
        $details = $this->client->getDetails($ip);

        return [
            'city' => $details->city ?? null,
            'latitude' => isset($details->loc) ? explode(',', $details->loc)[0] : null,
            'longitude' => isset($details->loc) ? explode(',', $details->loc)[1] : null,
        ];
    }
}
