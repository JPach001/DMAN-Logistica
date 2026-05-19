<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function send_json(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function request_payload(): array
{
    $raw = file_get_contents('php://input');
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return $_POST ?: $_GET;
}

function http_get_json(string $url, array $headers = []): array
{
    $normalizedHeaders = ['Accept: application/json'];
    foreach ($headers as $key => $value) {
        if (is_int($key)) {
            $normalizedHeaders[] = (string) $value;
        } else {
            $normalizedHeaders[] = $key . ': ' . $value;
        }
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => $normalizedHeaders,
        ]);
        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $status >= 400) {
            throw new RuntimeException($error !== '' ? $error : 'La solicitud HTTP falló.');
        }
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 12,
                'ignore_errors' => true,
                'header' => implode("\r\n", $normalizedHeaders),
            ],
        ]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            throw new RuntimeException('La solicitud HTTP falló.');
        }
    }

    $json = json_decode((string) $body, true);
    if (!is_array($json)) {
        throw new RuntimeException('La respuesta del servicio no es JSON válido.');
    }

    return $json;
}

function geoapify_geocode(string $address): array
{
    $apiKey = getenv('GEOAPIFY_API_KEY') ?: getenv('DMAN_GEOAPIFY_API_KEY') ?: '';
    if ($apiKey === '') {
        throw new RuntimeException('Falta la variable GEOAPIFY_API_KEY.');
    }

    $url = 'https://api.geoapify.com/v1/geocode/search?text=' . rawurlencode($address)
        . '&format=json&limit=1&lang=es&filter=countrycode:mx&apiKey=' . rawurlencode($apiKey);
    $json = http_get_json($url);

    $feature = $json['results'][0] ?? null;
    if (!is_array($feature) || !isset($feature['lat'], $feature['lon'])) {
        throw new RuntimeException('No se pudo ubicar la dirección.');
    }

    return [
        'lat' => (float) $feature['lat'],
        'lng' => (float) $feature['lon'],
        'formatted_address' => (string) ($feature['formatted'] ?? $address),
    ];
}

function geoapify_distance(array $origin, array $destination): float
{
    $apiKey = getenv('GEOAPIFY_API_KEY') ?: getenv('DMAN_GEOAPIFY_API_KEY') ?: '';
    if ($apiKey === '') {
        throw new RuntimeException('Falta la variable GEOAPIFY_API_KEY.');
    }

    $url = 'https://api.geoapify.com/v1/routing?waypoints='
        . rawurlencode($origin['lat'] . ',' . $origin['lng'])
        . '%7C'
        . rawurlencode($destination['lat'] . ',' . $destination['lng'])
        . '&mode=light_truck&format=json&units=metric&lang=es&apiKey=' . rawurlencode($apiKey);

    $json = http_get_json($url);
    $route = $json['results'][0] ?? null;

    if (!is_array($route) || !isset($route['distance'])) {
        throw new RuntimeException('No se pudo calcular la ruta.');
    }

    return ((float) $route['distance']) / 1000;
}

function osm_geocode(string $address): array
{
    $url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' . rawurlencode($address);
    $json = http_get_json($url, [
        'User-Agent' => 'DMAN-Logistica/1.0',
        'Accept-Language' => 'es-MX,es;q=0.9,en;q=0.8',
    ]);

    $item = $json[0] ?? null;
    if (!is_array($item) || !isset($item['lat'], $item['lon'])) {
        throw new RuntimeException('No se pudo ubicar la dirección.');
    }

    return [
        'lat' => (float) $item['lat'],
        'lng' => (float) $item['lon'],
        'formatted_address' => (string) ($item['display_name'] ?? $address),
    ];
}

function osm_distance(array $origin, array $destination): float
{
    $url = 'https://router.project-osrm.org/route/v1/driving/'
        . $origin['lng'] . ',' . $origin['lat']
        . ';' . $destination['lng'] . ',' . $destination['lat']
        . '?overview=false&alternatives=false&steps=false';
    $json = http_get_json($url, [
        'User-Agent' => 'DMAN-Logistica/1.0',
    ]);

    $route = $json['routes'][0] ?? null;
    if (!is_array($route) || !isset($route['distance'])) {
        throw new RuntimeException('No se pudo calcular la ruta.');
    }

    return ((float) $route['distance']) / 1000;
}

function resolver_distancia(string $originAddress, string $destinationAddress): array
{
    try {
        $origin = geoapify_geocode($originAddress);
        $destination = geoapify_geocode($destinationAddress);
        $distanceKm = geoapify_distance($origin, $destination);

        return [
            'distance_km' => $distanceKm,
            'source' => 'geoapify',
            'origin' => $origin['formatted_address'],
            'destination' => $destination['formatted_address'],
        ];
    } catch (Throwable $exception) {
        // Fallback libre por si Geoapify no responde o no existe la key.
    }

    $origin = osm_geocode($originAddress);
    $destination = osm_geocode($destinationAddress);
    $distanceKm = osm_distance($origin, $destination);

    return [
        'distance_km' => $distanceKm,
        'source' => 'osm-osrm',
        'origin' => $origin['formatted_address'],
        'destination' => $destination['formatted_address'],
    ];
}

$payload = request_payload();
$origin = trim((string) ($payload['origen'] ?? ''));
$destination = trim((string) ($payload['destino'] ?? ''));

if ($origin === '' || $destination === '') {
    send_json(400, [
        'success' => false,
        'message' => 'Debes escribir origen y destino.',
    ]);
}

try {
    $result = resolver_distancia($origin, $destination);
    send_json(200, [
        'success' => true,
        'message' => 'Ruta calculada correctamente.',
        'distance_km' => round((float) $result['distance_km'], 2),
        'source' => $result['source'],
        'origin' => $result['origin'],
        'destination' => $result['destination'],
    ]);
} catch (Throwable $exception) {
    send_json(500, [
        'success' => false,
        'message' => 'No se pudo calcular la distancia: ' . $exception->getMessage(),
    ]);
}
