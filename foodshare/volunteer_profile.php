<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Volunteer') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch volunteer profile
$query = "SELECT user_id, volunteer_name, email, mobile, occupation, experience, address, profile_pic FROM volunteer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Set default profile picture if none is uploaded
$profile_pic = !empty($profile['profile_pic']) ? $profile['profile_pic'] : 'default_profile.jpg';

// Fetch user's posts
$query = "SELECT id, media_path FROM volunteer_posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$posts = $stmt->get_result();
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
    <link rel="stylesheet" href="vprofile.css?v=2"> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        function openModal(src, type) {
            var modal = document.getElementById("mediaModal");
            var modalContent = document.getElementById("modalContent");
            modal.style.display = "flex";

            if (type === 'video') {
                modalContent.innerHTML = '<video src="' + src + '" controls autoplay style="width: 100%; max-height: 90vh;"></video>';
            } else {
                modalContent.innerHTML = '<img src="' + src + '" style="width: 100%; max-height: 90vh;">';
            }
        }

        function closeModal() {
            document.getElementById("mediaModal").style.display = "none";
        }
    </script>
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

<div class="profile-container">
    <div class="profile-header">
        <img src="uploads/<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic">
        <div class="profile-info">
            <h2><?= htmlspecialchars($profile['volunteer_name']) ?> <span class="user-id">(<?= htmlspecialchars($profile['user_id']) ?>)</span></h2>
            <p>Email: <?= htmlspecialchars($profile['email']) ?></p>
            <p>Mobile: <?= htmlspecialchars($profile['mobile']) ?></p>
            <p>Occupation: <?= htmlspecialchars($profile['occupation']) ?></p>
            <p>Experience: <?= htmlspecialchars($profile['experience']) ?></p>
            <p>Address: <?= htmlspecialchars($profile['address']) ?></p>
            <a href="edit_volunteer_profile.php" class="edit-profile">Edit Profile</a>
            <a href="volunteer_dashboard.php" class="back-btn">Back</a>
        </div>
    </div>

    <div class="post-section">
        <form action="upload_post.php" method="post" enctype="multipart/form-data">
            <input type="file" name="post_media" accept="image/*,video/*" required>
            <button type="submit">Post</button>
        </form>
    </div>

    <div class="gallery">
    <?php while ($post = $posts->fetch_assoc()): ?>
        <div class="gallery-item">
            <?php $media_path = "uploads/" . htmlspecialchars($post['media_path']); ?>
            <?php if (strpos($post['media_path'], '.mp4') !== false): ?>
                <video src="<?= $media_path ?>" class="media-thumbnail" onclick="openModal('<?= $media_path ?>', 'video')" muted></video>
            <?php else: ?>
                <img src="<?= $media_path ?>" class="media-thumbnail" onclick="openModal('<?= $media_path ?>', 'image')">
            <?php endif; ?>

            <!-- Delete Button with Confirmation -->
            <form action="delete_post.php" method="post" class="delete-form" onsubmit="return confirmDelete()">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button class="delete-btn"><i class="fas fa-trash-alt"></i></button>
            </form>

            <script>
                function confirmDelete() {
                    return confirm("Are you sure you want to delete this post?");
                }
            </script>

        </div>
    <?php endwhile; ?>
</div>

<!-- Fullscreen Modal -->
<div id="mediaModal" class="modal" onclick="closeModal()">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <div id="modalContent"></div>
</div>

</body>
</html>
