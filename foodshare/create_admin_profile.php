<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];  
    $phone_number = $_POST['phone_number'];
    $email_address = $_POST['email_address'];

    $query = "INSERT INTO admin_profiles (user_id, name, email_address, phone_number) 
              VALUES (?, ?, ?, ?)";
    
    if (!$stmt = $conn->prepare($query)) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("ssss", $user_id, $name, $email_address, $phone_number);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
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
    <title>Create Admin Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        body { 
            font-family: 'Montserrat', sans-serif; 
            background: linear-gradient(135deg, #fbc2eb, #a6c1ee); 
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .form-container { 
            background: rgba(255, 255, 255, 0.3); 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15); 
            width: 100%; 
            max-width: 400px; 
            backdrop-filter: blur(15px); 
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
            border-color: #5a95ff; 
            box-shadow: 0 0 8px rgba(90, 149, 255, 0.6); 
        }
        button { 
            background: linear-gradient(135deg, #6a85b6, #5a95ff); 
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
            background: linear-gradient(135deg, #5a95ff, #4a6ba6); 
            transform: translateY(-3px); 
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Admin Profile</h2>
        <form method="POST" action="">
            <label for="name">Name:</label>  
            <input type="text" name="name" id="name" required> 
            
            <label for="email_address">Email Address:</label>  
            <input type="email" name="email_address" id="email_address" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" required>
            
            <button type="submit">Create Profile</button>
        </form>
    </div>
</body>
</html>
