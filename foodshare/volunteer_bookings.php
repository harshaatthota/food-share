<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch volunteer profile
$query = "SELECT * FROM volunteer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Fetch bookings made by this volunteer
$myBookingsQuery = "
    SELECT fd.restaurant_name, fd.location, fd.rice_item, fd.curry_item, fd.other_item, 
           b.people_served, b.created_at, b.otp, b.status, b.id AS booking_id, fd.user_id AS restaurant_user_id
    FROM bookings b
    JOIN food_donations fd ON b.food_id = fd.id
    WHERE b.volunteer_id = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($myBookingsQuery);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$myBookingsResult = $stmt->get_result();

// Handle OTP validation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp']) && isset($_POST['booking_id'])) {
    $entered_otp = $_POST['otp'];
    $booking_id = $_POST['booking_id'];

    // Fetch the OTP and status of the booking
    $otpQuery = "SELECT otp, status FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($otpQuery);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $otpResult = $stmt->get_result()->fetch_assoc();

    if ($otpResult['otp'] === $entered_otp && $otpResult['status'] !== 'Collected') {
        // Update status to "Collected"
        $updateQuery = "UPDATE bookings SET status = 'Collected' WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        // Mark food as taken
        $updateFoodQuery = "UPDATE food_donations SET food_taken_by_volunteer = TRUE WHERE id = ?";
        $stmt = $conn->prepare($updateFoodQuery);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        echo "<script>alert('Food received successfully!'); window.location.href='volunteer_bookings.php';</script>";
        exit();
    } else {
        echo "<script>alert('Incorrect OTP or food already collected.');</script>";
    }
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
    <link rel="stylesheet" href="volunteer_bookings.css?v=2"> 
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
        <h1>Your Food Bookings</h1>

        <?php if ($myBookingsResult->num_rows > 0): ?>
            <?php while ($booking = $myBookingsResult->fetch_assoc()): ?>
                <div class="booking-card">
                <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($booking['restaurant_name']); ?> (<?php echo htmlspecialchars($booking['restaurant_user_id']); ?>)</p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                    <p><strong>Rice:</strong> <?php echo htmlspecialchars($booking['rice_item']); ?></p>
                    <p><strong>Curry:</strong> <?php echo htmlspecialchars($booking['curry_item']); ?></p>
                    <p><strong>Other:</strong> <?php echo htmlspecialchars($booking['other_item']); ?></p>
                    <p><strong>People Served:</strong> <?php echo htmlspecialchars($booking['people_served']); ?></p>
                    <p><strong>Booked On:</strong> <?php echo htmlspecialchars($booking['created_at']); ?></p>

                    <?php if ($booking['status'] === 'Pending'): ?>
                        <form class="otp-form" method="POST">
                            <label for="otp">Enter OTP:</label>
                            <input type="text" name="otp" required>
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                            <button type="submit" class="btn">Submit OTP</button>
                        </form>
                    <?php elseif ($booking['status'] === 'Collected'): ?>
                        <p>Food has already been collected.</p>
                    <?php else: ?>
                        <p>Booking status: <?php echo htmlspecialchars($booking['status']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have not booked any food donations yet.</p>
        <?php endif; ?>

        <a href="volunteer_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>

</html>
