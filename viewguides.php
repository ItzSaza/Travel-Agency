<?php
header('Content-Type: text/html');

$jsonFile = 'guidedetails.json';

if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true);

    foreach ($data as $guide) {
        echo '
        <div class="col-md-4 guide-item">
            <img src="'.htmlspecialchars($guide['image']).'" alt="Portrait of guide '.htmlspecialchars($guide['name']).'" class="guide-avatar" loading="lazy" width="160" height="160" />
            <h4>'.htmlspecialchars($guide['name']).'</h4>
            <p>'.htmlspecialchars($guide['desc']).'</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="tel:'.htmlspecialchars($guide['phone']).'" class="btn btn-outline-primary btn-sm"><i class="bi bi-telephone me-1"></i> Call</a>
                <a href="https://wa.me/'.preg_replace("/[^0-9]/", "", $guide['whatsapp']).'" target="_blank" class="btn btn-success btn-sm"><i class="bi bi-whatsapp me-1"></i> WhatsApp</a>
                <a href="mailto:'.htmlspecialchars($guide['email']).'" class="btn btn-danger btn-sm"><i class="bi bi-envelope me-1"></i> Email</a>
            </div>
        </div>';
    }
} else {
    echo "<p>No guides found.</p>";
}
?>
