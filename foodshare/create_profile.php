<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $restaurant_name = $_POST['restaurant_name'];
    $owner_name = $_POST['owner_name'];
    $address = $_POST['address'];
    $landmark = $_POST['landmark'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $query = "INSERT INTO restaurant_profiles (user_id, restaurant_name, owner_name, address, landmark, email, phone_number) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if (!$stmt = $conn->prepare($query)) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $user_id, $restaurant_name, $owner_name, $address, $landmark, $email, $phone_number);

    if ($stmt->execute()) {
        header("Location: restaurant_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Restaurant Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

body { 
    font-family: 'Montserrat', sans-serif; 
    background: linear-gradient(135deg, #a8e6cf, #dcedc1); 
    margin: 0; 
    padding: 50px 0; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    min-height: 100vh; 
    box-sizing: border-box; 
}
.form-container { 
    background: rgba(255, 255, 255, 0.5); 
    padding: 40px; 
    border-radius: 15px; 
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15); 
    width: 100%; 
    max-width: 400px; 
    backdrop-filter: blur(15px); 
    box-sizing: border-box; 
    margin: 50px 0; 
}
h2 { 
    text-align: center; 
    color: #333; 
    font-weight: 700; 
    margin-bottom: 30px; 
}
label { 
    display: block; 
    margin-bottom: 10px; 
    font-weight: 600; 
    color: #555; 
}
input[type="text"], 
input[type="email"] { 
    width: 100%; 
    padding: 14px; 
    border: 1px solid #ccc; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    background: #f9fcff; 
    font-size: 1rem; 
    color: #333; 
    outline: none; 
    box-sizing: border-box; 
}
input:focus { 
    border-color: #a8e6cf; 
    box-shadow: 0 0 8px rgba(168, 230, 207, 0.6); 
}
button { 
    background: linear-gradient(135deg, #45a29e, #66fcf1); 
    color: white; 
    padding: 14px; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer; 
    font-size: 1rem; 
    font-weight: 600; 
    width: 100%; 
    transition: all 0.3s ease; 
}
button:hover { 
    background: linear-gradient(135deg, #66fcf1, #1f2833); 
    transform: translateY(-3px); 
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Restaurant Profile</h2>
        <form method="POST" action="">
            <label for="restaurant_name">Restaurant Name:</label>
            <input type="text" name="restaurant_name" id="restaurant_name" required>
            
            <label for="owner_name">Owner Name:</label>
            <input type="text" name="owner_name" id="owner_name" required>
            
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required>
            
            <label for="landmark">Landmark:</label>
            <input type="text" name="landmark" id="landmark">
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" required>
            
            <button type="submit">Create Profile</button>
        </form>
    </div>
</body>
</html>
