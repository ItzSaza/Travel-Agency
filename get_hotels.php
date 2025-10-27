<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/hotels.json';
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$contents = file_get_contents($file);
if ($contents === false) {
    http_response_code(500);
    echo json_encode(['message' => 'Could not read hotels file']);
    exit;
}

$data = json_decode($contents, true);
if ($data === null) {
    // If malformed, return empty array
    echo json_encode([]);
    exit;
}

echo json_encode($data, JSON_PRETTY_PRINT);
?>
