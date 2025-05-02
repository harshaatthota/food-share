<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch collected donations (only bookings with status = 'Collected')
$query = "
    SELECT fd.restaurant_name, fd.location, fd.rice_item, fd.curry_item, fd.other_item, 
       fd.serves, b.volunteer_name, b.volunteer_id, b.people_served, b.id AS booking_id

    FROM bookings b
    JOIN food_donations fd ON b.food_id = fd.id
    WHERE fd.user_id = ? AND b.status = 'Collected'
    ORDER BY b.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare Connect</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="rdonations.css?v=2"> 
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
                <li class="nav-item"><a class="nav-link" href="restaurant_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="restaurant_donations.php">Donations</a></li>
                <li class="nav-item"><a class="nav-link" href="r_raise_complaint.php">Raise Complaint</a></li>
                <li class="nav-item"><a class="nav-link" href="restaurant_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>


<div class="container">
    <h1>Your Donations</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($donation = $result->fetch_assoc()): ?>
            <div class="booking-card">
            <p><strong>Volunteer:</strong> <?php echo htmlspecialchars($donation['volunteer_name']) . " (ID: " . htmlspecialchars($donation['volunteer_id']) . ")"; ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($donation['location']); ?></p>
                <p><strong>Rice:</strong> <?php echo htmlspecialchars($donation['rice_item']); ?></p>
                <p><strong>Curry:</strong> <?php echo htmlspecialchars($donation['curry_item']); ?></p>
                <p><strong>Other:</strong> <?php echo htmlspecialchars($donation['other_item']); ?></p>
                <p><strong>Booked:</strong> <?php echo htmlspecialchars($donation['people_served']); ?></p>
                <p class="text-success"><strong>Status:</strong> Food donated ✅</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="alert alert-warning">No food donations yet.</p>
    <?php endif; ?>

    <a href="restaurant_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>

</body>
</html>
