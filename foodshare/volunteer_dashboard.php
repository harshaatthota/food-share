<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch volunteer profile
$query = "SELECT user_id, volunteer_name, email, mobile, occupation, experience, address FROM volunteer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    header("Location: create_volunteer_profile.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="volunteer_dashboard.css?v=2"> 
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
                <li class="nav-item"><a class="nav-link" href="volunteer_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="volunteer_bookings.php">Bookings</a></li>
                <li class="nav-item"><a class="nav-link" href="v_raise_complaint.php">Raise Complaint</a></li>
                <li class="nav-item"><a class="nav-link" href="volunteer_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container">
        <div class="mission-container">
            <h1 class="welcome-text">Welcome, <?= htmlspecialchars($profile['volunteer_name']); ?>!</h1>
            <p id="mission-text" class="collapsed">
                FoodShare is a platform dedicated to bridging the gap between surplus food and those in need. 
                Volunteers play a crucial role in this mission by delivering donated food from restaurants...
            </p>
            <button id="toggle-mission-btn" class="learn-more-btn">Learn More</button>
        </div>

    <div id="food-list" class="card">
    <h2>Available Food Donations</h2>

    <!-- Left-aligned search bar -->
    <div class="search-bar-container mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by restaurant name, landmark, or address">
    <button class="btn search-btn" onclick="loadFoodDonations()">Search</button>
    </div>

    <div id="available-donations-container">
        <!-- Donations will be loaded here via AJAX -->
    </div>
</div>

    </div>

    <div class="thank-you-container">
        <p class="thank-you-text">
            We, the FoodShare members, are truly thankful for your unwavering support and commitment...
        </p>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
const originalText = 'Search by restaurant name, landmark, or address       '; // extra spaces for smooth scroll
let scrollIndex = 0;

function animatePlaceholder() {
    const text = originalText.substring(scrollIndex) + originalText.substring(0, scrollIndex);
    searchInput.setAttribute('placeholder', text);
    scrollIndex = (scrollIndex + 1) % originalText.length;
}

setInterval(animatePlaceholder, 200); // Adjust speed here

        function toggleMenu() {
            const menu = document.getElementById('menuDropdown');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        function toggleProfileMenu() {
            const profile = document.getElementById('profileDropdown');
            profile.style.display = profile.style.display === 'block' ? 'none' : 'block';
        }

        function loadFoodDonations() {
    const searchQuery = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_food_donations.php?search=' + encodeURIComponent(searchQuery), true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('available-donations-container').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}


        function openBookingBox(foodId) {
            document.getElementById('confirm-box-' + foodId).classList.remove('hidden');
        }

        function closeBookingBox(foodId) {
            document.getElementById('confirm-box-' + foodId).classList.add('hidden');
        }

        function confirmBooking(foodId) {
            const peopleServed = document.getElementById('people-' + foodId).value;

            if (peopleServed <= 0) {
                alert('Please enter a valid number of people.');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'book_food.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Food successfully booked!');
                        loadFoodDonations();
                    } else {
                        alert(response.error);
                    }
                }
            };

            xhr.send(`food_id=${foodId}&people_served=${peopleServed}`);
        }

        document.getElementById('toggle-mission-btn').addEventListener('click', function() {
            const missionText = document.getElementById('mission-text');
            const button = this;
            if (missionText.classList.contains('expanded')) {
                missionText.classList.remove('expanded');
                missionText.textContent = `FoodShare is a platform dedicated to bridging the gap between surplus food and those in need. Volunteers play a crucial role in this mission by delivering donated food from restaurants...`;
                button.textContent = 'Learn More';
            } else {
                missionText.classList.add('expanded');
                missionText.textContent = `FoodShare is a platform dedicated to bridging the gap between surplus food and those in need. Volunteers play a crucial role in this mission by delivering donated food from restaurants to individuals in need in their community, ensuring that food does not go to waste. Together, we can make a difference!`;
                button.textContent = 'Show Less';
            }
        });

        setInterval(loadFoodDonations, 30000);
        window.onload = loadFoodDonations;
    </script>

    <style>
        .menu-dropdown, .profile-dropdown {
            display: none;
        }
    </style>
    
</body>
</html>
