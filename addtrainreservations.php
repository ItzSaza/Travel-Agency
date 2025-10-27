<?php
header('Content-Type: application/json');

// Get JSON input
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

// Validate required fields
if (!isset($data['trainName']) || !isset($data['passengerName']) || !isset($data['date'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Read existing reservations
$jsonFile = 'trainreservations.json';
$currentData = [];

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $currentData = json_decode($jsonContent, true);
}

// Add new reservation with timestamp
$data['reservationId'] = uniqid();
$data['timestamp'] = date('Y-m-d H:i:s');

$currentData['reservations'][] = $data;

// Save back to file
if (file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Reservation added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving reservation']);
}
?>
