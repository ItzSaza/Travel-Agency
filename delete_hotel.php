<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$raw = file_get_contents('php://input');
$input = null;

if (stripos($contentType, 'application/json') !== false && $raw) {
    $input = json_decode($raw, true);
} else {
    $input = $_POST;
}

$id = trim($input['id'] ?? '');
$name = trim($input['name'] ?? '');
$location = trim($input['location'] ?? '');

if ($id === '' && $name === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Provide id or name to delete']);
    exit;
}

$file = __DIR__ . '/hotels.json';
if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['message' => 'Hotels file not found']);
    exit;
}

$fp = fopen($file, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['message' => 'Could not open hotels file']);
    exit;
}

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
    if ($data === null) $data = [];
}

$found = false;
foreach ($data as $idx => $item) {
    if ($id !== '' && isset($item['id']) && $item['id'] === $id) {
        unset($data[$idx]);
        $found = true;
        break;
    }
    // Fallback match by name+location
    if ($id === '' && $name !== '' && isset($item['name']) && $item['name'] === $name) {
        if ($location === '' || (isset($item['location']) && $item['location'] === $location)) {
            unset($data[$idx]);
            $found = true;
            break;
        }
    }
}

if ($found) {
    $data = array_values($data);
    rewind($fp);
    ftruncate($fp, 0);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(['message' => 'Hotel removed successfully']);
    exit;
} else {
    flock($fp, LOCK_UN);
    fclose($fp);
    http_response_code(404);
    echo json_encode(['message' => 'Hotel not found']);
    exit;
}

?>
