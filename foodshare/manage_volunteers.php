<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

// Fetch volunteers
$query_volunteers = "SELECT unique_id, email, role, blocked FROM users WHERE role = 'Volunteer' AND role != 'Admin'";
$result_volunteers = $conn->query($query_volunteers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare Connect</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #004400;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
        table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    background: var(--glassmorphism);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    table-layout: fixed; /* Fixes shifting issues */
}

th, td {
    padding: 15px;
    text-align: center; /* Keep text properly aligned */
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease-in-out;
}

th {
    background: var(--accent-color);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Fix column widths to prevent movement */
th:nth-child(1), td:nth-child(1) { width: 15%; } /* User ID */
th:nth-child(2), td:nth-child(2) { width: 30%; } /* Email */
th:nth-child(3), td:nth-child(3) { width: 15%; } /* Role */
th:nth-child(4), td:nth-child(4) { width: 10%; } /* Blocked */
th:nth-child(5), td:nth-child(5) { width: 20%; } /* Action */

tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.6);
}

tr:nth-child(odd) {
    background-color: rgba(255, 255, 255, 0.4);
}

tr:hover {
    background: rgba(0, 114, 255, 0.1);
    transition: 0.3s ease-in-out;
}

td {
    font-size: 16px;
    font-weight: 500;
    color: var(--text-color);
}

/* Fix button width to prevent shifting */
td a {
    background: var(--accent-color);
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    width: 90px; /* Fixed width */
    text-align: center;
    transition: background 0.3s;
}

td a:hover {
    background: var(--accent-hover);
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

<p><h1>Manage Restaurants</h1></p>


    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Blocked</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_volunteers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['unique_id'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['role'] ?></td>
                <td><?= $row['blocked'] ? 'Yes' : 'No' ?></td>
                <td>
    <?php if ($row['blocked']): ?>
        <a href="toggle_block.php?email=<?= $row['email'] ?>&action=unblock&role=Volunteer">Unblock</a>
    <?php else: ?>
        <a href="toggle_block.php?email=<?= $row['email'] ?>&action=block&role=Volunteer">Block</a>
    <?php endif; ?>
</td>

            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
