<?php
// Start session to access stored payment details
session_start();

// Retrieve stored session variables
$name = $_SESSION['name'] ?? 'Guest';
$amount = $_SESSION['amount'] ?? '0';
$payment_method = $_SESSION['payment_method'] ?? 'Unknown';

// Clear session data after displaying
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - FoodShare Connect</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('contri.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        .container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            display: inline-block;
        }
        .btn-home {
            margin-top: 20px;
            background-color: lightgreen;
            color: black;
            padding: 10px 20px;
            font-size: 1.2rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>🎉 Payment Successful!</h2>
        <p>Thank you, <strong><?php echo htmlspecialchars($name); ?></strong>, for your generous contribution!</p>
        <p>Amount Donated: <strong>₹<?php echo htmlspecialchars($amount); ?></strong></p>
        <p>Payment Method: <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $payment_method))); ?></strong></p>
        <p>Your contribution will help us fight food waste and feed those in need.</p>
        
        <a href="index.html" class="btn-home">Return to Home</a>
    </div>

</body>
</html>
