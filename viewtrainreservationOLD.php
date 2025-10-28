<?php
header('Content-Type: application/json');

$jsonFile = 'trainreservations.json';

if (!file_exists($jsonFile)) {
    echo json_encode(['success' => false, 'message' => 'No reservations file found']);
    exit;
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

if (!isset($_GET['id']) || $_GET['id'] === '') {
    echo json_encode(['success' => false, 'message' => 'Missing reservation id']);
    exit;
}

$id = (string)$_GET['id'];

$found = null;
if (isset($data['reservations']) && is_array($data['reservations'])) {
    foreach ($data['reservations'] as $res) {
        if ((string)($res['reservationId'] ?? '') === $id) {
            $found = $res;
            break;
        }
    }
}

if ($found) {
    echo json_encode(['success' => true, 'reservation' => $found]);
} else {
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
}
?>
