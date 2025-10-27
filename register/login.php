<?php
header("Content-Type: application/json");

// Path to your JSON data file
$jsonFile = "users.json";

// Decode the JSON data into a PHP array
if (!file_exists($jsonFile)) {
    echo json_encode(["status" => "error", "message" => "No users found."]);
    exit;
}

$data = json_decode(file_get_contents($jsonFile), true);

// Get login data from AJAX request
$input = json_decode(file_get_contents("php://input"), true);
$email = $input['email'];
$password = $input['password'];

// Check credentials
foreach ($data as $user) {
    if ($user['email'] === $email && $user['password'] === $password) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful!",
            "name" => $user['name']
        ]);
        exit;
    }
}

// If not matched
echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
?>