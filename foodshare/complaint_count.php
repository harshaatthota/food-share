<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "food_share";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_GET['id'];

// Fetch the real-time complaint count
$sql = "SELECT COUNT(*) AS total FROM complaints WHERE against_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $complaint_count = $row['total'];
    echo "User (ID: $user_id) has <strong>$complaint_count</strong> complaints.";
} else {
    echo "User not found.";
}

$conn->close();
?>
