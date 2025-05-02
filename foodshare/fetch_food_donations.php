<?php
require 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = strtolower($search);

if (!empty($search)) {
    $foodQuery = "
        SELECT fd.id, fd.restaurant_name, fd.location, rp.phone_number AS mobile, 
               fd.rice_item, fd.curry_item, fd.other_item, fd.serves, fd.created_at,
               COALESCE(SUM(CASE WHEN b.status != 'Cancelled' THEN b.people_served ELSE 0 END), 0) AS total_booked
        FROM food_donations fd
        LEFT JOIN bookings b ON fd.id = b.food_id
        LEFT JOIN restaurant_profiles rp ON fd.restaurant_name = rp.restaurant_name
        WHERE LOWER(fd.restaurant_name) LIKE ? 
           OR LOWER(fd.location) LIKE ?
           OR LOWER(rp.landmark) LIKE ?
        GROUP BY fd.id, fd.restaurant_name, fd.location, rp.phone_number, fd.rice_item, fd.curry_item, fd.other_item, fd.serves, fd.created_at
        HAVING total_booked < fd.serves
        ORDER BY fd.created_at DESC
    ";
    $like = "%$search%";
    $stmt = $conn->prepare($foodQuery);
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $foodResult = $stmt->get_result();
} else {
    $foodQuery = "
        SELECT fd.id, fd.restaurant_name, fd.location, rp.phone_number AS mobile, 
               fd.rice_item, fd.curry_item, fd.other_item, fd.serves, fd.created_at,
               COALESCE(SUM(CASE WHEN b.status != 'Cancelled' THEN b.people_served ELSE 0 END), 0) AS total_booked
        FROM food_donations fd
        LEFT JOIN bookings b ON fd.id = b.food_id
        LEFT JOIN restaurant_profiles rp ON fd.restaurant_name = rp.restaurant_name
        GROUP BY fd.id, fd.restaurant_name, fd.location, rp.phone_number, fd.rice_item, fd.curry_item, fd.other_item, fd.serves, fd.created_at
        HAVING total_booked < fd.serves
        ORDER BY fd.created_at DESC
    ";
    $foodResult = $conn->query($foodQuery);
}

if ($foodResult->num_rows > 0) {
    while ($food = $foodResult->fetch_assoc()) {
        $food_id = htmlspecialchars($food['id']);
        $restaurant_name = htmlspecialchars($food['restaurant_name']);
        $location = htmlspecialchars($food['location']);
        $mobile = htmlspecialchars($food['mobile']);
        $rice_item = htmlspecialchars($food['rice_item']);
        $curry_item = htmlspecialchars($food['curry_item']);
        $other_item = htmlspecialchars($food['other_item']);
        $serves = (int)$food['serves'];
        $total_booked = (int)$food['total_booked'];
        $remaining_serves = $serves - $total_booked;
        $created_at = date('d M Y, h:i A', strtotime($food['created_at']));
        ?>
        <div class="donation-item" id="food-card-<?php echo $food_id; ?>" style="width: 100%; max-width: 550px;">
            <p><strong>Restaurant:</strong> <?php echo $restaurant_name; ?></p>
            <p><strong>Location:</strong> <?php echo $location; ?></p>
            <p><strong>Mobile:</strong> <?php echo $mobile; ?></p>
            <p><strong>Rice:</strong> <?php echo $rice_item; ?></p>
            <p><strong>Curry:</strong> <?php echo $curry_item; ?></p>
            <p><strong>Other:</strong> <?php echo $other_item; ?></p>
            <p><strong>Remaining Serves:</strong> <span id="serves-<?php echo $food_id; ?>"><?php echo $remaining_serves; ?></span> people</p>
            <p><strong>Created At:</strong> <?php echo $created_at; ?></p>

            <button class="book-food-btn" style="padding: 10px 20px; font-size: 0.9rem;" onclick="openBookingBox(<?php echo $food_id; ?>)">Book Food</button>

            <div id="confirm-box-<?php echo $food_id; ?>" class="confirm-box hidden">
                <label>Number of People to Serve:</label>
                <input type="number" id="people-<?php echo $food_id; ?>" min="1" max="<?php echo $remaining_serves; ?>" required>
                <div class="button-group">
                    <button class="confirm-btn" style="padding: 4px 9px; font-size: 0.9rem;" onclick="confirmBooking(<?php echo $food_id; ?>)">Confirm</button>
                    <button class="cancel-btn" style="padding: 4px 9px; font-size: 0.9rem;" onclick="closeBookingBox(<?php echo $food_id; ?>)">Cancel</button>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p style='color:red;'>No available food donations found for your search.</p>";
}
?>
