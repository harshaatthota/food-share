<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch restaurant profile
$query = "SELECT * FROM restaurant_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    header("Location: create_profile.php");
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
    echo "<script>alert('Food donation submitted successfully!'); window.location.href='restaurant_dashboard.php';</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="rdashboard.css?v=2"> 
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


<!-- Welcome Text -->
<div class="welcome-text">
    <p id="short-text">
    Restaurants play a vital role in supporting FoodShare Connect by donating surplus food to help those in need. Every day, many restaurants have excess food that, instead of being wasted, can be redirected to feed hungry individuals and families. Through our platform, they can easily contribute fresh and nutritious meals to underprivileged communities.
    <p id="full-text" style="display: none;">
    Our dedicated volunteers play a crucial part in this process by collecting, sorting, and distributing food donations efficiently. They ensure that the donated food reaches those who need it the most in a timely manner. This collaboration helps reduce food waste while providing essential meals to struggling families. By partnering with FoodShare Connect, restaurants not only give back to their communities but also contribute to a sustainable solution for hunger. Our volunteers act as a bridge between donors and recipients, ensuring a smooth and organized food distribution process. The generosity of restaurants makes a significant impact in fighting food insecurity. Each donation helps bring hope and nourishment to someone in need. Through this initiative, we promote kindness, sustainability, and social responsibility. With more restaurants joining hands, we can expand our reach and feed more people. FoodShare Connect makes the donation process easy and effective for restaurants. Together, we are creating a network of sharing and caring. Our mission is to ensure that no meal goes to waste and no one goes hungry.
</p>
    <button id="toggle-btn" class="learn-more-btn" onclick="toggleText()">Learn More</button>
</div>
<script>
    function toggleText() {
        const shortText = document.getElementById('short-text');
        const fullText = document.getElementById('full-text');
        const button = document.getElementById('toggle-btn');

        if (fullText.style.display === 'none') {
            fullText.style.display = 'block';
            button.textContent = 'Show Less';
        } else {
            fullText.style.display = 'none';
            button.textContent = 'Learn More';
        }
    }
</script>

</a>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('dropdownMenu');
        const button = document.querySelector('.profile-btn');
        if (e.target !== button && !button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>


    <!-- Main Container -->
    <div class="container">

        <!-- Donate Food Form -->
        <div class="card">
            <h2>Donate Food</h2>
            <form method="POST" class="form-container">
                <label>Rice Item:</label>
                <input type="text" name="rice_item[]" required>

                <label>Curry Item:</label>
                <input type="text" name="curry_item[]" required>

                <label>Other Item:</label>
                <input type="text" name="other_item[]">

                <label>Serves (Number of People):</label>
                <input type="number" name="serves[]" min="1" required>

                <button type="submit" class="btn">Submit Donation</button>
            </form>
        </div>

        <!-- Donations Display -->
        <div class="card">
            <h2>Your Donations</h2>
            <div id="donations-container">
                <!-- AJAX loaded donations will appear here -->
            </div>
        </div>

    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('show');
        }

        function loadDonations() {
            fetch('fetch_restaurant_donations.php')
                .then(response => response.text())
                .then(data => document.getElementById('donations-container').innerHTML = data);
        }

        setInterval(loadDonations, 30000);
        window.onload = loadDonations;
    </script>
<!-- Closing Text -->
<div class="welcome-text" style="margin-top: 30px; font-size: 1rem;">
    <p><strong>At FoodShare, we ensure that every meal you donate reaches the hands of those in need. Our dedicated volunteers work tirelessly to bridge the gap between surplus and scarcity, bringing hope and nourishment to the less fortunate. Together, let's fight hunger—one donation at a time.</strong></p>
</div>

</body>
</html> 