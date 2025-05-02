<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT unique_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Simulate sending reset link (Replace with actual email logic)
        echo "<script>alert('A password reset link has been sent to your email.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Email not found! Please try again.'); window.location.href='forgot_password.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FoodShare Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: lightgreen;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            color: black;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #004400;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            text-align: left;
        }

        input[type="email"],
        .btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus {
            border-color: #32cd32;
            box-shadow: 0 0 5px rgba(50, 205, 50, 0.5);
        }

        .btn {
            background-color: #004400;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 5px;
            text-align: center;
        }

        .btn:hover {
            background-color: #32cd32;
        }

        .btn:active {
            background-color: #28a745;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Enter your email to receive a password reset link.</p>
        <form action="forgot_password.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter your registered email" required>

            <button type="submit" class="btn">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
