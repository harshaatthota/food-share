<?php
session_start();
require 'db.php';

// Ensure the user is a 'Volunteer'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$food_id = $_POST['food_id'];
$people_served = (int)$_POST['people_served'];

// Validate the number of people served
if ($people_served <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid number of people']);
    exit();
}

// Fetch volunteer's name from the `volunteer_profiles` table
$getVolunteerQuery = "SELECT volunteer_name FROM volunteer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($getVolunteerQuery);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($volunteer_name);
$stmt->fetch();
$stmt->close();

if (!$volunteer_name) {
    echo json_encode(['success' => false, 'error' => 'Volunteer profile not found']);
    exit();
}

// Fetch restaurant name and total serves from `food_donations`
$getFoodQuery = "SELECT restaurant_name, serves FROM food_donations WHERE id = ?";
$stmt = $conn->prepare($getFoodQuery);
$stmt->bind_param("i", $food_id);
$stmt->execute();
$stmt->bind_result($restaurant_name, $total_serves);
$stmt->fetch();
$stmt->close();

if (!$restaurant_name) {
    echo json_encode(['success' => false, 'error' => 'Food donation not found']);
    exit();
}

// Calculate total serves already booked for the food item
$getBookedQuery = "SELECT IFNULL(SUM(people_served), 0) FROM bookings WHERE food_id = ? AND status != 'Cancelled'";
$stmt = $conn->prepare($getBookedQuery);
$stmt->bind_param("i", $food_id);
$stmt->execute();
$stmt->bind_result($total_booked);
$stmt->fetch();
$stmt->close();

$remaining_serves = $total_serves - $total_booked;

if ($people_served > $remaining_serves) {
    echo json_encode(['success' => false, 'error' => 'Not enough serves available']);
    exit();
}

// Generate 6-digit OTP
$otp = mt_rand(100000, 999999);

// Insert the booking into the `bookings` table with the OTP and 'Pending' status
$insertBookingQuery = "INSERT INTO bookings (food_id, volunteer_id, volunteer_name, restaurant_name, people_served, otp, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($insertBookingQuery);
$stmt->bind_param("isssis", $food_id, $user_id, $volunteer_name, $restaurant_name, $people_served, $otp);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Booking failed: ' . $stmt->error]);
    $stmt->close();
    exit();
}

$stmt->close();

// Send OTP to the restaurant (this is a placeholder for the future integration with SMS or email)
$message = "New booking made by $volunteer_name. OTP for collection: $otp.";

// Example SMS or email integration could go here
// sendSMS($restaurantPhoneNumber, $message);
// sendEmail($restaurantEmail, 'Food Pickup OTP', $message);

// Calculate updated remaining serves
$remaining_serves -= $people_served;

// Return success response with updated remaining serves and OTP (for testing/debugging purposes)
echo json_encode([
    'success' => true,
    'remainingServes' => $remaining_serves,
    'otp' => $otp, // For debugging/testing purposes
]);
?>
