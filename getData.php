<?php
header('Content-Type: application/json');
$data = file_get_contents('data.json');
$records = json_decode($data, true);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    foreach ($records as $record) {
        if ($record['id'] == $id) {
            echo json_encode($record);
            exit;
        }
    }
    echo json_encode(["message" => "Record not found"]);
} else {
    echo json_encode($records);
}
?>
