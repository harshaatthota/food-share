<?php
if (!isset($_GET['unique_id'])) {
    header("Location: register.php");
    exit();
}
$unique_id = htmlspecialchars($_GET['unique_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link rel="icon" href="foodshare.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="register.css?v=3">
</head>
<body>

<div class="container mt-5 text-center">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-3">Registration Successful!</h2>
        <p class="fw-bold fs-4">Your Unique ID: <span class="text-primary"><?php echo $unique_id; ?></span></p>
        <p class="text-muted">Please remember your Unique ID for login purposes.</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-success me-2">Go to Login</a>
            <a href="index.php" class="btn btn-secondary">Go to Home</a>
        </div>
    </div>
</div>

</body>
</html>
