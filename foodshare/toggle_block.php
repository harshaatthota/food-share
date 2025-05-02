<?php
require 'db.php';

// Ensure the user is an admin
session_start();
if ($_SESSION['role'] != 'Admin') {
    header("Location: index.php");
    exit();
}

// Check if email and action are set
if (!isset($_GET['email']) || !isset($_GET['action']) || !isset($_GET['role'])) {
    die("Invalid request.");
}

$email = $_GET['email'];
$action = $_GET['action'];
$role = $_GET['role'];  // Get user role (Volunteer or Restaurant)

// Validate action
if ($action !== 'block' && $action !== 'unblock') {
    die("Invalid action.");
}

// Determine the new blocked status
$new_status = ($action == 'block') ? 1 : 0;

// Prepare the SQL query to update the blocked status
$query = "UPDATE users SET blocked = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $new_status, $email);

if ($stmt->execute()) {
    // Redirect based on the role of the user being blocked/unblocked
    if ($role === 'Volunteer') {
        header("Location: manage_volunteers.php");
    } elseif ($role === 'Restaurant') {
        header("Location: manage_restaurants.php");
    } else {
        header("Location: admin_dashboard.php"); // Fallback case
    }
} else {
    echo "Error updating user status: " . $stmt->error;
}

$stmt->close();
$conn->close();
exit();
?>
