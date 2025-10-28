<?php
// File: viewreviews.php
header("Content-Type: application/json");

$file = "reviews.json";

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo json_encode([]);
}
?>
