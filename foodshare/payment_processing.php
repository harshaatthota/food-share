<?php
// Start session to store user details
session_start();

// Get form data
$name = $_POST['name'] ?? '';
$amount = $_POST['amount'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Store in session (to display on the confirmation page)
$_SESSION['name'] = $name;
$_SESSION['amount'] = $amount;
$_SESSION['payment_method'] = $payment_method;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
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
    </style>
</head>
<body>

    <div class="container">
        <h2>Redirecting for Payment Processing...</h2>
        <p>Please wait while we process your payment.</p>
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script>
        // Redirect to success page after 3 seconds
        setTimeout(function() {
            window.location.href = "payment_success.php";
        }, 3000);
    </script>

</body>
</html>
