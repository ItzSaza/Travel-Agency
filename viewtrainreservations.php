<?php
header('Content-Type: application/json');

$jsonFile = 'trainreservations.json';


$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

// Optional filtering by train name
if (isset($_GET['train'])) {
    $trainName = $_GET['train'];
    $filtered = array_filter($data['reservations'], function($reservation) use ($trainName) {
        return strcasecmp($reservation['trainName'], $trainName) === 0;
    });
    echo json_encode(['success' => true, 'reservations' => array_values($filtered)]);
} elseif (isset($_GET['id'])) { //searching by ID
    $reservationId = $_GET['id'];
    $filtered = array_filter($data['reservations'], function($reservation) use ($reservationId) {
        return $reservation['reservationId'] == $reservationId;
    });
    echo json_encode(['success' => true, 'reservation' => array_values($filtered)[0] ?? null]);
} else {
    echo json_encode(['success' => true, 'reservations' => $data['reservations']]);
}
?>
