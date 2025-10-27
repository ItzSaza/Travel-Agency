<?php
header('Content-Type: application/json');

$jsonFile = 'trainreservations.json';

if (!file_exists($jsonFile)) {
    echo json_encode(['success' => false, 'message' => 'No reservations found']);
    exit;
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

// Optional filtering by train name
if (isset($_GET['train'])) {
    $trainName = $_GET['train'];
    $filtered = array_filter($data['reservations'], function($reservation) use ($trainName) {
        return strcasecmp($reservation['trainName'], $trainName) === 0;
    });
    echo json_encode(['success' => true, 'reservations' => array_values($filtered)]);
} else {
    echo json_encode(['success' => true, 'reservations' => $data['reservations']]);
}
?>
