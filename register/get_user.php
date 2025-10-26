<?php

header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/users.json';

// If the file is missing, return empty array (so the UI still works)
if (!file_exists($file)) {
    echo "[]";
    exit;
}

// Try to read the file safely
$fp = fopen($file, 'r');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to open users.json']);
    exit;
}

if (!flock($fp, LOCK_SH)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Could not lock users.json']);
    exit;
}

$contents = stream_get_contents($fp);
flock($fp, LOCK_UN);
fclose($fp);

// If contents are invalid, still return an empty array
$contents = trim($contents);
echo $contents !== '' ? $contents : '[]';
