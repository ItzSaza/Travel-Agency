<?php
header("Content-Type: application/json");

// Read the existing data
$data = json_decode(file_get_contents("users.json"), true);

// Read input from AJAX
$input = json_decode(file_get_contents("php://input"), true);

// Add new record
$newUser = array(
    "fullname" => $input["fullname"],
    "email" => $input["email"],
    "password" => $input["password"],
    "country" => $input["country"],
    "gender" => $input["gender"]
);

$data[] = $newUser;

// Save back to JSON file
file_put_contents("users.json", json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(["status" => "success", "message" => "User added successfully!"]);
?>
