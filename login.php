<?php
header('Content-Type: application/json; charset=utf-8');

// Path to the users.json file
$file = __DIR__ . '/users.json';

// Read existing users
if (!file_exists($file)) {
    echo json_encode(["status" => "error", "message" => "No users found."]);
    exit;
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) $data = [];

// Get JSON input from AJAX
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

// Check credentials
$found = false;
foreach ($data as $user) {
    // Compare email case-insensitive
    if (strcasecmp($user['email'], $email) === 0 && $user['password'] === $password) {
        $found = true;
        echo json_encode([
            "status" => "success",
            "message" => "Login successful!",
            "name" => $user['fullname']
        ]);
        break;
    }
}

// If not matched
if (!$found) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
}
?>