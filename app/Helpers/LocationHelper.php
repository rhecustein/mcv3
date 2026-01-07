<?php

namespace App\Helpers;

use Illuminate\Http\Request;

/**
 * Location Helper
 * Helper methods for IP address and location tracking
 */
class LocationHelper
{
    /**
     * Get client IP address
     *
     * @param Request|null $request
     * @return string
     */
    public static function getClientIP(?Request $request = null): string
    {
        $request = $request ?? request();

        // Check for proxy headers first
        if ($request->server('HTTP_CF_CONNECTING_IP')) {
            // Cloudflare
            return $request->server('HTTP_CF_CONNECTING_IP');
        }

        if ($request->server('HTTP_X_FORWARDED_FOR')) {
            // Load balancer or proxy
            $ips = explode(',', $request->server('HTTP_X_FORWARDED_FOR'));
            return trim($ips[0]);
        }

        if ($request->server('HTTP_X_REAL_IP')) {
            // Nginx proxy
            return $request->server('HTTP_X_REAL_IP');
        }

        // Direct connection
        return $request->ip() ?? '127.0.0.1';
    }

    /**
     * Get location information from IP address
     * Note: This is a placeholder. In production, integrate with a GeoIP service
     *
     * @param string|null $ip
     * @return array
     */
    public static function getLocationFromIP(?string $ip = null): array
    {
        $ip = $ip ?? self::getClientIP();

        // For localhost/private IPs, return default
        if (self::isPrivateIP($ip)) {
            return [
                'ip' => $ip,
                'city' => 'Local',
                'country' => 'ID',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        // TODO: Integrate with GeoIP service (e.g., ipapi.co, ip-api.com, MaxMind)
        // For now, return basic info
        return [
            'ip' => $ip,
            'city' => 'Unknown',
            'country' => 'Unknown',
            'latitude' => null,
            'longitude' => null,
        ];
    }

    /**
     * Check if IP is private/local
     *
     * @param string $ip
     * @return bool
     */
    public static function isPrivateIP(string $ip): bool
    {
        // Check for localhost
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Check for private IP ranges
        $privateRanges = [
            '10.0.0.0' => '10.255.255.255',
            '172.16.0.0' => '172.31.255.255',
            '192.168.0.0' => '192.168.255.255',
        ];

        $ipLong = ip2long($ip);

        if ($ipLong === false) {
            return false;
        }

        foreach ($privateRanges as $start => $end) {
            if ($ipLong >= ip2long($start) && $ipLong <= ip2long($end)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse coordinates from string
     *
     * @param string|null $coordinateString
     * @return array{latitude: float|null, longitude: float|null}
     */
    public static function parseCoordinates(?string $coordinateString): array
    {
        if (empty($coordinateString)) {
            return ['latitude' => null, 'longitude' => null];
        }

        // Try comma-separated format: "lat,lng"
        if (str_contains($coordinateString, ',')) {
            $parts = explode(',', $coordinateString);
            return [
                'latitude' => isset($parts[0]) ? (float) trim($parts[0]) : null,
                'longitude' => isset($parts[1]) ? (float) trim($parts[1]) : null,
            ];
        }

        // Try space-separated format: "lat lng"
        if (str_contains($coordinateString, ' ')) {
            $parts = explode(' ', $coordinateString);
            return [
                'latitude' => isset($parts[0]) ? (float) trim($parts[0]) : null,
                'longitude' => isset($parts[1]) ? (float) trim($parts[1]) : null,
            ];
        }

        return ['latitude' => null, 'longitude' => null];
    }

    /**
     * Get tracking data for result creation
     *
     * @param Request|null $request
     * @return array
     */
    public static function getTrackingData(?Request $request = null): array
    {
        $ip = self::getClientIP($request);
        $location = self::getLocationFromIP($ip);

        return [
            'created_ip' => $ip,
            'created_city' => $location['city'],
            'created_latitude' => $location['latitude'],
            'created_longitude' => $location['longitude'],
        ];
    }
}
