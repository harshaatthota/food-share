<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["post_media"])) {
    $user_id = $_SESSION['user_id'];
    $upload_dir = "uploads/";

    // Create uploads folder if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES["post_media"];
    $file_name = basename($file["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = ["jpg", "jpeg", "png", "gif", "mp4"];

    // Validate file type
    if (!in_array($file_ext, $allowed_exts)) {
        die("Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, MP4.");
    }

    // Generate unique file name
    $new_file_name = uniqid("post_", true) . "." . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    // Move uploaded file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Insert into database
        $query = "INSERT INTO admin_posts (user_id, media_path, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $user_id, $new_file_name);
        
        if ($stmt->execute()) {
            header("Location: admin_profile.php"); // Redirect to admin profile
            exit();
        } else {
            die("Database error: " . $stmt->error);
        }
    } else {
        die("Error uploading file.");
    }
} else {
    die("No file uploaded.");
}
?>
