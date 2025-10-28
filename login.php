<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/users.json';
$data = json_decode(file_get_contents($file), true);

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

// Check credentials
foreach ($data as $user) {
    if ($user['email'] === $email && $user['password'] === $password) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful!",
            "name" => $user['fullname']   //FIXED field name
        ]);
        exit;
    }
}

// If not matched
echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
?>