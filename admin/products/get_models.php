<?php
include '../includes/config.php';

$brand_id = $_GET['brand_id'];
$result = $conn->query("SELECT * FROM vehicle_models WHERE brand_id = $brand_id");

$models = [];
while ($row = $result->fetch_assoc()) {
    $models[] = $row;
}

echo json_encode($models);
?>   