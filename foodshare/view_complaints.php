<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "food_share";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM complaints ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>Complaints List (Admin View)</h2>";
echo "<table border='1'>
<tr>
    <th>ID</th>
    <th>Complainant Type</th>
    <th>Complainant ID</th>
    <th>Against Type</th>
    <th>Against ID</th>
    <th>Description</th>
    <th>Created At</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['complainant_type']."</td>
            <td>".$row['complainant_id']."</td>
            <td>".$row['against_type']."</td>
            <td>".$row['against_id']."</td>
            <td>".$row['description']."</td>
            <td>".$row['created_at']."</td>
          </tr>";
}

echo "</table>";
$conn->close();
?>
