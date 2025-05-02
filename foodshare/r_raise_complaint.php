<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$dbname = "food_share";  // Change if needed

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complainant_type = $_POST['complainant_type'];
    $complainant_id = $_POST['complainant_id'];
    $against_type = $_POST['against_type'];
    $against_id = $_POST['against_id'];
    $description = $_POST['description'];

    // Convert "Individual" to "Restaurant" for storage
    if ($complainant_type == "Individual") {
        $complainant_type = "Restaurant";
    }
    if ($against_type == "Individual") {
        $against_type = "Restaurant";
    }

    // ID Validation: Must be exactly 5 digits
    if (!preg_match('/^\d{5}$/', $complainant_id) || !preg_match('/^\d{5}$/', $against_id)) {
        echo "<script>alert('User IDs must be exactly 5 digits!'); window.history.back();</script>";
        exit();
    }

    // Validate Volunteer ID (must start with 002)
    if ($complainant_type == "Volunteer" && !preg_match('/^002\d{2}$/', $complainant_id)) {
        echo "<script>alert('Volunteer IDs must start with 002!'); window.history.back();</script>";
        exit();
    }

    if ($against_type == "Volunteer" && !preg_match('/^002\d{2}$/', $against_id)) {
        echo "<script>alert('Complaints against Volunteers must have IDs starting with 002!'); window.history.back();</script>";
        exit();
    }

    // Validate Restaurant & Individual IDs (both must start with 003)
    if ($complainant_type == "Restaurant" && !preg_match('/^003\d{2}$/', $complainant_id)) {
        echo "<script>alert('Restaurant & Individual IDs must start with 003!'); window.history.back();</script>";
        exit();
    }

    if ($against_type == "Restaurant" && !preg_match('/^003\d{2}$/', $against_id)) {
        echo "<script>alert('Complaints against Restaurants & Individuals must have IDs starting with 003!'); window.history.back();</script>";
        exit();
    }

    // Check if both complainant and accused exist in users table
    $check_complainant = $conn->query("SELECT * FROM users WHERE unique_id = '$complainant_id'");
    $check_against = $conn->query("SELECT * FROM users WHERE unique_id = '$against_id'");

    if ($check_complainant->num_rows == 0 || $check_against->num_rows == 0) {
        echo "<script>alert('Either complainant or accused does not exist in the system!'); window.history.back();</script>";
        exit();
    }

    // Insert complaint into database (store full 5-digit IDs)
    $sql = "INSERT INTO complaints (complainant_type, complainant_id, against_type, against_id, description) 
            VALUES ('$complainant_type', '$complainant_id', '$against_type', '$against_id', '$description')";

    if ($conn->query($sql) === TRUE) {
        // Update complaint count in the users table
        $update_sql = "UPDATE users SET complaint_count = complaint_count + 1 WHERE unique_id = '$against_id'";
        $conn->query($update_sql);

        echo "<script>
                alert('Complaint submitted successfully!');
                window.location.href='r_raise_complaint.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
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
/* Centered Complaint Form */
form {
    width: 90%;
    max-width: 600px;
    background: var(--overlay-bg);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin: 50px auto; /* Centers it */
}

/* Form Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 700;
    color: var(--text-color);
}

/* Label Styling */
label {
    display: block;
    font-weight: 500;
    margin: 10px 0 5px;
}

/* Input, Select, and Textarea Styling */
select, input, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
    background: white;
    color: var(--text-color);
    transition: 0.3s;
}

select:focus, input:focus, textarea:focus {
    border-color: var(--accent-color);
    outline: none;
    box-shadow: 0 0 5px rgba(214, 41, 41, 0.5);
}

/* Textarea */
textarea {
    resize: none;
    height: 120px;
}

/* Submit Button */
button {
    width: 100%;
    padding: 12px;
    background: var(--accent-color);
    border: none;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 15px;
    transition: 0.3s ease;
}

button:hover {
    background: #a91d1d;
}
/* Glassmorphic Contact Info Section */
.contact-info {
    width: 90%;
    max-width: 500px;
    background: rgba(255, 255, 255, 0.2); /* Glass effect */
    padding: 20px;
    border-radius: 15px;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-align: left;
    margin: 30px auto; /* Centers the entire box */
    transition: all 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center; /* Centers content inside */
    padding-left: 0; /* Remove left padding */
    padding-right: 0;
}

/* Contact Info Items */
.contact-info .info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.1rem;
    color: #000; /* Black text */
    font-weight: 600;
    width: 100%;
    justify-content: center; /* Centers text & icon */
}

/* Icons */
.contact-info i {
    font-size: 1.5rem;
    color: #000; /* Black icons */
    width: 30px; /* Ensures all icons are aligned */
    text-align: center;
}

/* Links */
.contact-info a {
    text-decoration: none;
    font-weight: 600;
    color: #000; /* Black text */
    transition: 0.3s;
}

.contact-info a:hover {
    color: #333; /* Slightly darker on hover */
}

/* Hover Effect */
.contact-info:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
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
                <li class="nav-item"><a class="nav-link" href="restaurant_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="restaurant_donations.php">Donations</a></li>
                <li class="nav-item"><a class="nav-link" href="r_raise_complaint.php">Raise Complaint</a></li>
                <li class="nav-item"><a class="nav-link" href="restaurant_profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
    <h2>Submit a Complaint</h2>
    <form action="" method="post">
        <label for="complainant_type">Who are you?</label>
        <select name="complainant_type" required>
            <option value="Volunteer">Volunteer</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Individual">Individual</option>
        </select>

        <label for="complainant_id">Your User ID:</label>
        <input type="number" name="complainant_id" required>

        <label for="against_type">Complaint Against:</label>
        <select name="against_type" required>
            <option value="Volunteer">Volunteer</option>
            <option value="Restaurant">Restaurant</option>
            <option value="Individual">Individual</option>
        </select>

        <label for="against_id">User ID of the Person You’re Complaining About:</label>
        <input type="number" name="against_id" required>

        <label for="description">Complaint Description:</label>
        <textarea name="description" required></textarea>

        <button type="submit">Submit Complaint</button>
    </form>
</body>
</html>
