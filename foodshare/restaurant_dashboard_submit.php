<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch restaurant profile
$query = "SELECT * FROM restaurant_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

if (!$profile) {
    echo json_encode(["status" => "error", "message" => "Profile not found"]);
    exit();
}

$location = $profile['address'] . ', ' . $profile['landmark'];

// Handle food donation submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rice_item'])) {
    $rice_items = $_POST['rice_item'];
    $curry_items = $_POST['curry_item'];
    $other_items = $_POST['other_item'];
    $serves = $_POST['serves'];

    foreach ($rice_items as $index => $rice_item) {
        $curry_item = $curry_items[$index];
        $other_item = $other_items[$index];
        $serves_people = $serves[$index];

        $insert_query = "INSERT INTO food_donations (user_id, restaurant_name, owner_name, location, rice_item, curry_item, other_item, serves, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssssi", $user_id, $profile['restaurant_name'], $profile['owner_name'], $location, $rice_item, $curry_item, $other_item, $serves_people);
        $stmt->execute();
    }

    echo json_encode(["status" => "success", "message" => "Donation added successfully!"]);
    exit();
}
?>
