<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch admin profile details
$query = "SELECT * FROM admin_profiles WHERE user_id = ?";
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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Handle Profile Picture Upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = time() . '_' . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], "uploads/$profile_pic");
        $updatePic = "UPDATE admin_profiles SET profile_pic = ? WHERE user_id = ?";
        $stmtPic = $conn->prepare($updatePic);
        $stmtPic->bind_param("ss", $profile_pic, $user_id);
        $stmtPic->execute();
    }

    // Update admin profile
    $update_query = "UPDATE admin_profiles SET name = ?, email_address = ?, phone_number = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssss", $name, $email, $phone_number, $user_id);
    $update_profile_success = $stmt->execute();

    if ($update_profile_success) {
        // Update email in `users` table
        $update_user_email_query = "UPDATE users SET email = ? WHERE unique_id = ?";
        $stmt = $conn->prepare($update_user_email_query);
        $stmt->bind_param("ss", $email, $user_id);
        $stmt->execute();
    
        echo "<script>alert('Profile updated successfully!'); window.location.href='admin_profile.php';</script>";
        exit();
    }
    else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Profile</title>
    <link rel="stylesheet" href="aedit_profile.css">
</head>
<body>

<div class="edit-profile-container">
    <h2>Edit Admin Profile</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Profile Pic Preview -->
        <div class="profile-pic-section">
            <img id="profilePreview" src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic-preview">
            <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        </div>

        <div class="form-container">
            <!-- Right Section -->
            <div class="right-section">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profile['email_address']) ?>" required>

                <label>Phone Number:</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($profile['phone_number']) ?>" required>
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

</div>

</body>
</html>
