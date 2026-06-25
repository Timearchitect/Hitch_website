<?php
// API gateway for routing /api/{server}/{endpoint...} to internal Node services.

declare(strict_types=1);

$serverName = isset($_GET['server']) ? trim((string)$_GET['server']) : '';
$endpoint = isset($_GET['endpoint']) ? trim((string)$_GET['endpoint']) : '';

if ($serverName === '') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Missing server name']);
    exit;
}

$serverPorts = [
    // hitchapp_rest_api.js
    'rest' => 40888,
    'hitchapp_rest_api' => 40888,

    // autosearches.js
    'autosearches' => 40889,

    // ogpicimage.js
    'ogpic' => 40890,
    'ogpicimage' => 40890,

    // hitch_sms_app.js
    'sms' => 41620,
    'hitch_sms_app' => 41620,
];

if (!array_key_exists($serverName, $serverPorts)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Unknown server',
        'server' => $serverName,
    ]);
    exit;
}

$port = $serverPorts[$serverName];
$path = '/' . ltrim($endpoint, '/');
if ($path === '/') {
    $path = '';
}

$forwardQuery = $_GET;
unset($forwardQuery['server'], $forwardQuery['endpoint']);
$queryString = http_build_query($forwardQuery);

$targetUrl = 'https://localhost:' . $port . $path;
if ($queryString !== '') {
    $targetUrl .= '?' . $queryString;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$body = file_get_contents('php://input');

$forwardHeaders = [];
if (function_exists('getallheaders')) {
    $incomingHeaders = getallheaders();
    foreach ($incomingHeaders as $name => $value) {
        $lower = strtolower($name);
        if ($lower === 'host' || $lower === 'content-length') {
            continue;
        }
        $forwardHeaders[] = $name . ': ' . $value;
    }
}

$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
if ($remoteAddr !== '') {
    $forwardHeaders[] = 'X-Forwarded-For: ' . $remoteAddr;
}
$forwardHeaders[] = 'X-Forwarded-Proto: https';
$forwardHeaders[] = 'X-Forwarded-Host: ' . ($_SERVER['HTTP_HOST'] ?? 'hitchapp.se');

$responseHeaders = [];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function ($curl, $headerLine) use (&$responseHeaders) {
    $len = strlen($headerLine);
    $header = trim($headerLine);

    if ($header === '' || stripos($header, 'HTTP/') === 0) {
        return $len;
    }

    $parts = explode(':', $header, 2);
    if (count($parts) === 2) {
        $name = trim($parts[0]);
        $value = trim($parts[1]);
        $responseHeaders[] = [$name, $value];
    }

    return $len;
});

if ($body !== false && $body !== '' && $method !== 'GET' && $method !== 'HEAD') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$responseBody = curl_exec($ch);
$curlErrNo = curl_errno($ch);
$curlError = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlErrNo !== 0 || $responseBody === false) {
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Gateway upstream request failed',
        'details' => $curlError,
        'server' => $serverName,
        'port' => $port,
    ]);
    exit;
}

http_response_code($httpCode > 0 ? $httpCode : 200);

$blockedHeaders = [
    'transfer-encoding',
    'content-length',
    'connection',
    'keep-alive',
    'upgrade',
    'proxy-authenticate',
    'proxy-authorization',
    'te',
    'trailers',
];

foreach ($responseHeaders as $pair) {
    [$name, $value] = $pair;
    if (in_array(strtolower($name), $blockedHeaders, true)) {
        continue;
    }

    // Replace duplicate headers except Set-Cookie, which can be repeated.
    $replace = strtolower($name) !== 'set-cookie';
    header($name . ': ' . $value, $replace);
}

echo $responseBody;
