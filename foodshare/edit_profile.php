<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch current restaurant profile details
$query = "SELECT * FROM restaurant_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    echo "Profile not found.";
    exit();
}

// Set default profile picture if not uploaded
$profile_pic = !empty($profile['profile_pic']) ? $profile['profile_pic'] : 'default_profile.jpg';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_name = $_POST['restaurant_name'];
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $landmark = $_POST['landmark'];

    $location = $address . ', ' . $landmark;

    // Handle Profile Picture Upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = time() . '_' . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], "uploads/$profile_pic");
        $updatePic = "UPDATE restaurant_profiles SET profile_pic = ? WHERE user_id = ?";
        $stmtPic = $conn->prepare($updatePic);
        $stmtPic->bind_param("ss", $profile_pic, $user_id);
        $stmtPic->execute();
    }

    // Update restaurant profile
    $update_query = "UPDATE restaurant_profiles SET restaurant_name=?, owner_name=?, email=?, phone_number=?, address=?, landmark=? WHERE user_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssss", $restaurant_name, $owner_name, $email, $phone_number, $address, $landmark, $user_id);
    $update_profile_success = $stmt->execute();

    if ($update_profile_success) {
        // Update email in `users` table
        $update_user_email_query = "UPDATE users SET email=? WHERE unique_id=?";
        $stmt = $conn->prepare($update_user_email_query);
        $stmt->bind_param("ss", $email, $user_id);
        $stmt->execute();

        // Update `food_donations` table (restaurant_name, owner_name, location)
        $update_food_query = "UPDATE food_donations SET restaurant_name=?, owner_name=?, location=? WHERE user_id=?";
        $stmt = $conn->prepare($update_food_query);
        $stmt->bind_param("ssss", $restaurant_name, $owner_name, $location, $user_id);
        $stmt->execute();

        // Update `bookings` table (restaurant_name)
        $update_booking_query = "UPDATE bookings SET restaurant_name=? WHERE food_id IN (SELECT id FROM food_donations WHERE user_id=?)";
        $stmt = $conn->prepare($update_booking_query);
        $stmt->bind_param("ss", $restaurant_name, $user_id);
        $stmt->execute();

        echo "<script>alert('Profile updated successfully!'); window.location.href='restaurant_profile.php';</script>";
exit();

    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Restaurant Profile</title>
    <link rel="stylesheet" href="redit_profiles.css"> 
</head>
<body>

<div class="edit-profile-container">
    <h2>Edit Profile</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Profile Pic Preview -->
        <div class="profile-pic-section">
            <img id="profilePreview" src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic-preview">
            <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        </div>


    <div class="form-container">
        <!-- Right Section -->
        <div class="right-section">
            <label>Restaurant Name:</label>
            <input type="text" name="restaurant_name" value="<?= htmlspecialchars($profile['restaurant_name']) ?>" required>

            <label>Owner Name:</label>
            <input type="text" name="owner_name" value="<?= htmlspecialchars($profile['owner_name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>

            <label>Phone Number:</label>
            <input type="tel" name="phone_number" value="<?= htmlspecialchars($profile['phone_number']) ?>" required>
        </div>

        <!-- Left Section -->
        <div class="left-section">
            <label>Address:</label>
            <textarea name="address" required><?= htmlspecialchars($profile['address']) ?></textarea>

            <label>Landmark:</label>
            <input type="text" name="landmark" value="<?= htmlspecialchars($profile['landmark']) ?>" required>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="submit-button">
        <button type="submit">Save Changes</button>
    </div>
</form>

<script>
    // Preview uploaded profile picture
    document.getElementById("profilePicInput").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profilePreview").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
