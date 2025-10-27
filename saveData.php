<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_POST['image'] ?? '';
    $region = $_POST['region'] ?? '';
    $rating = $_POST['rating'] ?? '';

    $newDestination = [
        "name" => $name,
        "description" => $description,
        "image" => $image,
        "region" => $region,
        "rating" => $rating
    ];

    $file = 'destinations.json';
    $data = [];

    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
    }

    $data[] = $newDestination;

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["message" => "Destination added successfully!"]);
} else {
    echo json_encode(["message" => "Invalid request."]);
}
?>
