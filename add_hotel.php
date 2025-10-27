<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // CORS preflight
    http_response_code(200);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$raw = file_get_contents('php://input');
$input = null;

if (stripos($contentType, 'application/json') !== false && $raw) {
    $input = json_decode($raw, true);
    if ($input === null) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON body']);
        exit;
    }
} else {
    // Fallback to form-encoded POST
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
$description = trim($input['description'] ?? '');
$image = trim($input['image'] ?? '');
$location = trim($input['location'] ?? '');

if ($name === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Field "name" is required']);
    exit;
}

$newHotel = [
    'name' => $name,
    'description' => $description,
    'image' => $image,
    'location' => $location
];

$file = __DIR__ . '/hotels.json';

// Ensure file exists
if (!file_exists($file)) {
    file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
}

$fp = fopen($file, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['message' => 'Could not open hotels file']);
    exit;
}

// Exclusive lock while we read/modify/write
if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(['message' => 'Could not lock hotels file']);
    exit;
}

$contents = '';
$filesize = filesize($file);
if ($filesize > 0) {
    rewind($fp);
    $contents = stream_get_contents($fp);
}

$data = [];
if ($contents !== '') {
    $data = json_decode($contents, true);
    if ($data === null) {
        // If file is corrupted, reset to empty array to avoid breaking the API
        $data = [];
    }
}

$data[] = $newHotel;

// Truncate and write updated data
rewind($fp);
ftruncate($fp, 0);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['message' => 'Hotel added successfully', 'hotel' => $newHotel]);
?>
