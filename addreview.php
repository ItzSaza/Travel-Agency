<?php
// File: addreview.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file = "reviews.json";

    // Get data from POST request
    $name = $_POST["name"];
    $email = $_POST["email"];
    $rating = $_POST["rating"];
    $comments = $_POST["comments"];

    // Read existing reviews
    $data = [];
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true) ?? [];
    }

    // Add new review
    $newReview = [
        "name" => $name,
        "email" => $email,
        "rating" => $rating,
        "comments" => $comments,
        "timestamp" => date("Y-m-d H:i:s")
    ];

    array_unshift($data, $newReview); // add to top
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["status" => "success"]);
}
?>
