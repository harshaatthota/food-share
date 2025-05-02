<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "food_share";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    // Use prepared statements for security
    $stmt = $conn->prepare("SELECT against_id FROM complaints WHERE id = ?");
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $against_id = $row['against_id'];

        // Delete the complaint
        $delete_stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
        $delete_stmt->bind_param("i", $complaint_id);
        if ($delete_stmt->execute()) {
            
            // Count remaining complaints for the accused
            $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM complaints WHERE against_id = ?");
            $count_stmt->bind_param("s", $against_id);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $count_row = $count_result->fetch_assoc();
            $new_complaint_count = $count_row['total'];

            // Update users table with new count
            $update_stmt = $conn->prepare("UPDATE users SET complaint_count = ? WHERE unique_id = ?");
            $update_stmt->bind_param("is", $new_complaint_count, $against_id);
            $update_stmt->execute();

            echo "<script>alert('Complaint deleted successfully!'); window.location.href='manage_complaints.php';</script>";
        } else {
            echo "Error deleting complaint.";
        }
    } else {
        echo "<script>alert('Complaint not found!'); window.history.back();</script>";
    }
}

$conn->close();
?>
