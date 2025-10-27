<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataFile = 'data.json';
    
    // Check if file exists and is writable
    if (!file_exists($dataFile)) {
        echo json_encode(["error" => "data.json file does not exist"]);
        exit;
    }
    
    if (!is_writable($dataFile)) {
        echo json_encode(["error" => "data.json is not writable"]);
        exit;
    }
    
    $jsonContent = file_get_contents($dataFile);
    if ($jsonContent === false) {
        echo json_encode(["error" => "Could not read data.json"]);
        exit;
    }
    
    $data = json_decode($jsonContent, true);
    if ($data === null) {
        echo json_encode(["error" => "Invalid JSON in data.json: " . json_last_error_msg()]);
        exit;
    }

    $newRecord = [
        "id" => count($data) + 1,
        "name" => $_POST['name'],
        "description" => $_POST['description'],
        "image" => $_POST['image'],
        "region" => $_POST['region'],
        "rating" => intval($_POST['rating'])
    ];

    $data[] = $newRecord;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(["message" => "New destination added successfully!"]);
}
?>
