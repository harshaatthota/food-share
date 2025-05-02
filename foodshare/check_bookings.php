<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

// Fetch booking details along with relevant data from food_donations and users table
$query = "
    SELECT b.id, b.food_id, b.volunteer_id, b.volunteer_name, b.restaurant_name, 
           b.people_served, b.otp, b.status, f.rice_item, f.curry_item, f.other_item
    FROM bookings b
    JOIN food_donations f ON b.food_id = f.id
    WHERE b.status != 'Cancelled'
";
$result = $conn->query($query);
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
    <style>
/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap');

:root {
    --primary-bg: #d6eaff; /* Light blue background */
    --text-color: #121212;
    --overlay-bg: rgba(255, 255, 255, 0.8);
    --accent-color: #0072ff; /* Blue accent */
    --accent-hover: #005bb5;
    --border-color: rgba(0, 0, 0, 0.1);
    --glassmorphism: rgba(255, 255, 255, 0.3);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

/* Body Styling */
body {
    background: var(--primary-bg) !important; /* Ensuring light blue background */
    color: var(--text-color);
    overflow-x: hidden;
}

/* Navbar */
.custom-navbar {
    background: transparent;
    padding: 20px 50px;
    border-bottom: 1px solid var(--border-color);
}

.custom-navbar .navbar-brand {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-color);
}

.custom-navbar .nav-link {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-color);
    margin: 0 15px;
    position: relative;
    transition: 0.3s ease-in-out;
}

.custom-navbar .nav-link:hover {
    color: var(--accent-color);
}

.custom-navbar .nav-link::after {
    content: "";
    display: block;
    width: 0;
    height: 2px;
    background: var(--accent-color);
    transition: width 0.3s;
    margin-top: 5px;
}

.custom-navbar .nav-link:hover::after {
    width: 100%;
}

html {
    overflow-y: scroll;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 16px; /* Increased padding */
    text-align: left;
    border-bottom: 1px solid rgba(0, 0, 0, 0.15);
    transition: background 0.3s ease-in-out;
    font-size: 16px;
}

th {
    background: #4B0082; /* Deep blueish-purple */
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Alternating Row Colors */
tr:nth-child(even) {
    background-color: rgba(230, 230, 255, 0.6); /* Light pastel blue */
}

tr:nth-child(odd) {
    background-color: rgba(240, 240, 255, 0.4); /* Slightly darker pastel */
}

tr:hover {
    background: rgba(75, 0, 130, 0.1); /* Soft hover effect */
}

/* Table Responsiveness */
@media (max-width: 768px) {
    th, td {
        font-size: 14px; /* Adjust font size for smaller screens */
        padding: 12px;
    }
}

    </style>
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

<p><h1>Check Bookings</h1></p>

    <div class="container">

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Volunteer Name</th>
                        <th>Restaurant Name</th>
                        <th>Food Items</th>
                        <th>People Served</th>
                        <th>OTP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['volunteer_name'] ?></td>
                        <td><?= $row['restaurant_name'] ?></td>
                        <td>
                            <strong>Rice:</strong> <?= $row['rice_item'] ?><br>
                            <strong>Curry:</strong> <?= $row['curry_item'] ?><br>
                            <strong>Other:</strong> <?= $row['other_item'] ?>
                        </td>
                        <td><?= $row['people_served'] ?></td>
                        <td><?= $row['otp'] ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>

    </div>

</body>
</html>

<?php
$conn->close();
