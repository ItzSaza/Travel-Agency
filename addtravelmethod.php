<?php
// File path
$jsonFile = "travelMethods.json";

// Get existing json data
$jsonData = json_decode(file_get_contents($jsonFile), true);

// New incoming data (POST method)
$newMethod = [
    "id" => intval($_POST["id"]),
    "type" => $_POST["type"],
    "name" => $_POST["name"],
    "icon" => $_POST["icon"],
    "description" => $_POST["description"],
    "priceRange" => $_POST["priceRange"],
    "availableCountries" => explode(",", $_POST["availableCountries"]),
    "duration" => $_POST["duration"],
    "rating" => floatval($_POST["rating"]),
    "images" => explode(",", $_POST["images"]),
    "bookingLink" => $_POST["bookingLink"]
];

// Add new item to JSON array
$jsonData["travelMethods"][] = $newMethod;

// Save updated JSON
file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

// Response
echo json_encode(["message" => "Travel method added successfully!"]);
?>
