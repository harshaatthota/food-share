<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch post data
    $query = "SELECT media_path FROM admin_posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if ($post) {
        // Delete media file from server
        $file_path = "uploads/" . $post['media_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete post from database
        $query = "DELETE FROM admin_posts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $post_id, $user_id);
        $stmt->execute();

        header("Location: admin_profile.php");
        exit();
    }
}

header("Location: admin_profile.php");
exit();
?>
