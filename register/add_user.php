<?php
// add_user.php
header('Content-Type: application/json; charset=utf-8');

try {
    // Always use an absolute path so PHP knows exactly where the JSON file is.
    $file = __DIR__ . '/users.json';

    // Ensure file exists
    if (!file_exists($file)) {
        // Create an empty array file
        if (false === @file_put_contents($file, "[]", LOCK_EX)) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Cannot create users.json (permission issue).']);
            exit;
        }
    }

    // Read JSON body
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON body.']);
        exit;
    }

    // Basic validation (adjust as you like)
    $required = ['fullname', 'email', 'password'];
    foreach ($required as $key) {
        if (empty($input[$key])) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $key"]);
            exit;
        }
    }

    // Open file for read/write, lock exclusively
    $fp = fopen($file, 'c+');
    if (!$fp) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to open users.json']);
        exit;
    }
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Could not lock users.json']);
        exit;
    }

    // Read existing content
    rewind($fp);
    $contents = stream_get_contents($fp);
    $users = json_decode($contents ?: '[]', true);
    if (!is_array($users)) { $users = []; }

    // Prevent duplicate emails (case-insensitive)
    foreach ($users as $u) {
        if (isset($u['email']) && strcasecmp($u['email'], $input['email']) === 0) {
            flock($fp, LOCK_UN);
            fclose($fp);
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
            exit;
        }
    }

    // Append new user (for demo: plaintext password; for real apps use password_hash)
    $users[] = [
        'fullname' => $input['fullname'],
        'email'    => $input['email'],
        'password' => $input['password'],
        'country'  => $input['country'] ?? '',
        'gender'   => $input['gender']  ?? ''
    ];

    // Write back pretty JSON
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        flock($fp, LOCK_UN);
        fclose($fp);
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to encode JSON']);
        exit;
    }

    // Truncate and overwrite
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, $json);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    echo json_encode(['status' => 'success', 'message' => 'User added successfully']);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
