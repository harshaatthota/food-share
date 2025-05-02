<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "food_share";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch complaints from database (including IDs)
$sql = "SELECT c.id, 
               c.complainant_id, u1.email AS complainant_email, 
               c.against_id, u2.email AS against_email, 
               c.complainant_type, c.against_type, c.description, c.created_at
        FROM complaints c
        JOIN users u1 ON c.complainant_id = u1.unique_id
        JOIN users u2 ON c.against_id = u2.unique_id
        ORDER BY c.created_at DESC";

$result = $conn->query($sql);
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
/* Ensure the table is at the top */
.container {
    display: flex;
    justify-content: center;
    align-items: flex-start; /* Aligns table to the top */
    min-height: auto; /* Removes unnecessary vertical centering */
    margin-top: 20px; /* Adds some space from the navbar */
}

/* Table Styling */
.custom-table {
    width: 100%;
    max-width: 1200px;
    border-collapse: collapse;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(12px);
    border-radius: 12px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Table Headers */
.custom-table th {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    color: white;
    font-weight: 700;
    padding: 15px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
}

/* Table Rows */
.custom-table td {
    padding: 15px;
    text-align: center;
    font-size: 16px;
    color: #000; /* Changed text color to black */
    font-weight: 500;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

/* First Row Below Heading */
.custom-table tbody tr:first-child {
    background: rgba(0, 204, 255, 0.6); /* Light Cyan */
    font-weight: 600;
    color: #000; /* Ensure the text stays black */
}

/* Hover Effect */
.custom-table tbody tr:hover {
    background: rgba(0, 123, 255, 0.3);
    transition: 0.3s ease-in-out;
}

/* Delete Button */
.custom-table td a {
    display: inline-block;
    padding: 8px 14px;
    background: #ff4d4d;
    color: white;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
    font-weight: 600;
    border: none;
    outline: none;
}

.custom-table td a:hover {
    background: #d9534f;
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 992px) {
    .container {
        overflow-x: auto;
        padding: 10px;
    }

    .custom-table th,
    .custom-table td {
        font-size: 14px;
        padding: 10px;
    }

    .custom-table td a {
        padding: 6px 10px;
        font-size: 12px;
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


<p><h1>Manage Complaints</h1></p>
    <div class="container mt-4">
   <table class="custom-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Complainant ID</th>
            <th>Complainant</th>
            <th>Accused ID</th>
            <th>Accused</th>
            <th>Complaint Type</th>
            <th>Accused Type</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['complainant_id']}</td>
                        <td>{$row['complainant_email']}</td>
                        <td>{$row['against_id']}</td>
                        <td>{$row['against_email']}</td>
                        <td>{$row['complainant_type']}</td>
                        <td>{$row['against_type']}</td>
                        <td>{$row['description']}</td>
                        <td>
                            <a href='delete_complaint.php?complaint_id={$row['id']}' class='btn btn-danger btn-sm'
                               onclick='return confirm(\"Are you sure you want to delete this complaint?\")'>
                               Delete
                            </a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No complaints found.</td></tr>";
        }
        ?>
    </tbody>
</table>

    </div>
</body>
</html>

<?php $conn->close(); ?>
