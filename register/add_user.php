<?php
// header("Content-Type: application/json");

// // Read the existing data
// $data = json_decode(file_get_contents("users.json"), true);

// // Read input from AJAX
// $input = json_decode(file_get_contents("php://input"), true);

// // Add new record
// $newUser = array(
//     "fullname" => $input["fullname"],
//     "email" => $input["email"],
//     "password" => $input["password"],
//     "country" => $input["country"],
//     "gender" => $input["gender"]
// );

// $data[] = $newUser;

// // Save back to JSON file
// file_put_contents("users.json", json_encode($data, JSON_PRETTY_PRINT));

// echo json_encode(["status" => "success", "message" => "User added successfully!"]);






header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/users.json';

// Read input
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Basic validation (add more as needed)
$required = ['fullname','email','password','country','gender'];
foreach ($required as $k) {
    if (!isset($input[$k]) || $input[$k] === '') {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>"Missing field: $k"]);
        exit;
    }
}
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Invalid email']);
    exit;
}

// Load existing data (create empty if missing/bad)
$existing = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $tmp = json_decode($json, true);
    if (is_array($tmp)) {
        $existing = $tmp;
    }
}

// Append new user
$newUser = [
    'fullname' => $input['fullname'],
    'email'    => $input['email'],
    'password' => $input['password'], // NOTE: don't store plaintext in production
    'country'  => $input['country'],
    'gender'   => $input['gender'],
];
$existing[] = $newUser;

// Safe write with file lock
$fp = fopen($file, 'c+'); // create if not exists
if (!$fp) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Cannot open users.json for writing']);
    exit;
}
flock($fp, LOCK_EX);
ftruncate($fp, 0);
fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['status'=>'success','message'=>'User added successfully!']);

?>
