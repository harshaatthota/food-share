<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $volunteer_name = $_POST['volunteer_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $occupation = $_POST['occupation'];
    $experience = $_POST['experience'];
    $address = $_POST['address'];

    // Check if email already exists
    $checkQuery = "SELECT email FROM volunteer_profiles WHERE email = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('This email is already linked to a volunteer profile.');</script>";
    } else {
        // Insert new volunteer profile
        $query = "INSERT INTO volunteer_profiles (user_id, volunteer_name, email, mobile, occupation, experience, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("sssssss", $user_id, $volunteer_name, $email, $mobile, $occupation, $experience, $address);

        if ($stmt->execute()) {
            // Update email in users table
            $updateUserQuery = "UPDATE users SET email=? WHERE unique_id=?";
            $updateUserStmt = $conn->prepare($updateUserQuery);
            $updateUserStmt->bind_param("ss", $email, $user_id);
            $updateUserStmt->execute();

            echo "<script>alert('Profile created successfully!'); window.location.href = 'volunteer_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
    $checkStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Volunteer Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #ff7f50, #20b2aa);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 50px 20px;
            margin: 0;
            color: #333;
        }

        .container {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            font-size: 1em;
            margin-bottom: 8px;
            text-align: left;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: calc(100% - 24px); /* Match button size */
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            background: rgba(255, 255, 255, 0.7);
            color: #333;
            outline: none;
        }

        input::placeholder, textarea::placeholder {
            color: #777;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Create Volunteer Profile</h2>
        <form method="POST" action="">
            <label for="volunteer_name">Volunteer Name:</label>
            <input type="text" id="volunteer_name" name="volunteer_name" placeholder="Enter your full name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" placeholder="Enter your mobile number" required>

            <label for="occupation">Occupation:</label>
            <select id="occupation" name="occupation" required>
                <option value="Student">Student</option>
                <option value="Job">Job</option>
                <option value="Other">Other</option>
            </select>

            <label for="experience">Experience in Social Helping (Optional):</label>
            <textarea id="experience" name="experience" rows="3" placeholder="Briefly describe your experience"></textarea>

            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3" placeholder="Enter your address" required></textarea>

            <button type="submit" class="btn">Create Profile</button>
        </form>
    </div>
</body>

</html>
