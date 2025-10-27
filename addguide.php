<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newGuide = [
        'name' => $_POST['name'],
        'desc' => $_POST['desc'],
        'phone' => $_POST['phone'],
        'whatsapp' => $_POST['whatsapp'],
        'email' => $_POST['email'],
        'image' => $_POST['image'] ?: 'g1.webp'
    ];

    $file = 'guidedetails.json';
    $guides = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $guides[] = $newGuide;
    file_put_contents($file, json_encode($guides, JSON_PRETTY_PRINT));

    echo json_encode(['status' => 'success', 'message' => 'Guide added successfully']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
