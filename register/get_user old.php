<?php
// header("Content-Type: application/json");
// $data = json_decode(file_get_contents("user.json"), true);
// echo json_encode($data);






header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/users.json'; // use the same file name everywhere

if (!file_exists($file)) {
    // Initialize empty array on first run
    echo json_encode([]);
    exit;
}

$json = file_get_contents($file);
$data = json_decode($json, true);

// If JSON malformed, fall back to empty list
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

echo json_encode($data);

?>
