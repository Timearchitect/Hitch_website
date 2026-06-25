<?php
$tripId = isset($_GET['tripId']) ? $_GET['tripId'] : null;

if (!$tripId) {
    http_response_code(400);
    exit('tripId is required');
}

// Only allow safe trip ID characters (alphanumeric, dash, underscore)
if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $tripId)) {
    http_response_code(400);
    exit('Invalid tripId');
}

$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$isFacebookBot = stripos($userAgent, 'facebookexternalhit') !== false || stripos($userAgent, 'facebot') !== false || stripos($userAgent, 'facebook.com') !== false;
$rectangle = $isFacebookBot ? '?rectangle=true' : '';
$nodeUrl = "https://localhost:40890/generate-trip-image/" . urlencode($tripId) . $rectangle;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $nodeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$imageData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $imageData !== false) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    echo $imageData;
} else {
    // Fall back to the default static image so Facebook's scraper always gets a valid
    // image instead of a 502, which would cause the OG card to show broken/no image.
    $defaultImage = __DIR__ . '/res/default_image1.png';
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=300'); // shorter TTL so it retries soon
    readfile($defaultImage);
}
