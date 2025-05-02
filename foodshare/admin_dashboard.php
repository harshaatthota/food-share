<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch admin profile details
$query = "SELECT * FROM admin_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    header("Location: create_profile.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare Connect</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="adashboard.css?v=2"> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.html" style="margin-left: -20px;">
            <img src="logo.png" alt="FoodShare Logo" height="60" class="me-2"> 
            <span class="fs-3 fw-bold">FoodShare Connect</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="check_bookings.php">Bookings</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_volunteers.php"></i>Volunteer</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_restaurants.php"></i>Restaurants</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_complaints.php"></i>Complaints</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_profile.php"><i class="fas fa-user"></i>Profile</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <div class="glass-container p-4 rounded-4 shadow-lg">
        <h2 class="fw-bold mb-3 text-dark">Welcome, <?php echo htmlspecialchars($profile['name']); ?> 👋</h2>
        <p class="text-dark fs-5">
            As the <strong>Admin</strong> of <span class="text-primary">FoodShare Connect</span>, you have full control over the platform's operations. Here's what you can do:
        </p>
        <ul class="text-dark fs-5">
            <li>🔍 <strong>Monitor bookings</strong> made by volunteers across the platform</li>
            <li>🚫 <strong>Block or unblock users</strong> (volunteers & restaurants)</li>
            <li>📨 <strong>Review and respond to complaints</strong> submitted by users</li>
        </ul>
        <p class="text-dark fs-5 mb-0">Use the navigation bar to manage all activities and keep FoodShare running smoothly!</p>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #eef2f3, #dfe9f3);
        font-family: 'Montserrat', sans-serif;
    }

    .glass-container {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
    }
</style>

</body>
</html>
