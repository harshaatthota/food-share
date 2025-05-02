<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch current profile data
$query = "SELECT volunteer_name, email, mobile, occupation, experience, address, profile_pic FROM volunteer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Set default profile picture if none exists
$profile_pic = !empty($profile['profile_pic']) ? $profile['profile_pic'] : 'default_profile.jpg';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['volunteer_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $occupation = $_POST['occupation'];
    $experience = $_POST['experience'];
    $address = $_POST['address'];
    $new_profile_pic = $profile_pic; // Default to existing profile pic

    // Handle Profile Picture Upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $upload_dir = "uploads/";
        $new_profile_pic = time() . '_' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $new_profile_pic);

        // Update profile picture in database
        $updatePicQuery = "UPDATE volunteer_profiles SET profile_pic = ? WHERE user_id = ?";
        $stmtPic = $conn->prepare($updatePicQuery);
        $stmtPic->bind_param("ss", $new_profile_pic, $user_id);
        $stmtPic->execute();
    }

    // Update other details
    $updateQuery = "UPDATE volunteer_profiles SET volunteer_name=?, email=?, mobile=?, occupation=?, experience=?, address=? WHERE user_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssss", $name, $email, $mobile, $occupation, $experience, $address, $user_id);
    $stmt->execute();

    header("Location: volunteer_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="vedit_profiles.css">
</head>
<body>

<div class="edit-profile-container">
    <h2>Edit Profile</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Profile Picture Section -->
        <div class="profile-pic-section">
            <img id="profilePreview" src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic-preview">
            <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        </div>

        <!-- Form Sections -->
        <div class="form-container">
            <!-- Right Section -->
            <div class="right-section">
                <label>Name:</label>
                <input type="text" name="volunteer_name" value="<?= htmlspecialchars($profile['volunteer_name']) ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>

                <label>Mobile:</label>
                <input type="text" name="mobile" value="<?= htmlspecialchars($profile['mobile']) ?>" required>
            </div>
            <!-- Left Section -->
            <div class="left-section">
                <label>Occupation:</label>
                <input type="text" name="occupation" value="<?= htmlspecialchars($profile['occupation']) ?>">
                <textarea name="experience"><?= htmlspecialchars($profile['experience']) ?></textarea>
                <label>Experience:</label>

                <label>Address:</label>
                <textarea name="address"><?= htmlspecialchars($profile['address']) ?></textarea>
                        </div>
        </div>

        <!-- Submit Button -->
        <div class="submit-button">
            <button type="submit">Save Changes</button>
        </div>
    </form>
</div>

<script>
    // Profile picture preview on upload
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
