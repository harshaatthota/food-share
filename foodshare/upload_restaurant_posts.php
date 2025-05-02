<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['post_media']) && $_FILES['post_media']['error'] == 0) {
    $file = $_FILES['post_media'];
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_file_name = "restaurant_{$user_id}_" . time() . "." . $file_ext;  // New structured file name
    $target_dir = "uploads/";
    $target_file = $target_dir . $new_file_name;

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['error'] = "Invalid file type! Only JPG, PNG, GIF, and MP4 are allowed.";
        header("Location: restaurant_profile.php");
        exit();
    }

    // Move file to uploads folder
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Insert into database
        $query = "INSERT INTO restaurant_posts (user_id, media_path, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $user_id, $new_file_name);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Restaurant Post uploaded successfully!";
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to upload file.";
    }
} else {
    $_SESSION['error'] = "Invalid request!";
}

header("Location: restaurant_profile.php");
exit();
?>
