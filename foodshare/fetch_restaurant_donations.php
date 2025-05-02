<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Restaurant') {
    http_response_code(403);
    exit('Unauthorized access');
}

$user_id = $_SESSION['user_id'];

$fetch_donations_query = "
    SELECT 
        fd.id AS donation_id, fd.rice_item, fd.curry_item, fd.other_item, fd.serves, fd.created_at,
        COALESCE(SUM(b.people_served), 0) AS total_served,
        GROUP_CONCAT(
            b.id, '|', b.volunteer_name, '|', b.volunteer_id, '|', IFNULL(vp.mobile, 'N/A'), '|', b.people_served, '|', b.otp, '|', b.status
            SEPARATOR ';'
        ) AS bookings
    FROM food_donations fd
    LEFT JOIN bookings b ON fd.id = b.food_id AND b.status != 'Cancelled'
    LEFT JOIN volunteer_profiles vp ON b.volunteer_id = vp.user_id
    WHERE fd.user_id = ?
    GROUP BY fd.id
    HAVING NOT (
        COUNT(b.id) > 0
        AND SUM(CASE WHEN b.status = 'Collected' THEN 1 ELSE 0 END) = COUNT(b.id)
        AND SUM(b.people_served) >= fd.serves
    )
    ORDER BY fd.created_at DESC
";

$stmt = $conn->prepare($fetch_donations_query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No donations found.</p>";
} else {
    while ($donation = $result->fetch_assoc()) {
        $total_served = (int)$donation['total_served'];
        $serves = (int)$donation['serves'];

        // Process bookings into an array
        $bookings = [];
        if (!empty($donation['bookings'])) {
            $booking_entries = explode(';', $donation['bookings']);
            foreach ($booking_entries as $entry) {
                $parts = explode('|', $entry);
                if (count($parts) === 7) {
                    $bookings[] = [
                        'booking_id' => $parts[0],
                        'volunteer_name' => $parts[1],
                        'volunteer_id' => $parts[2],
                        'mobile' => $parts[3],
                        'people_served' => $parts[4],
                        'otp' => $parts[5],
                        'status' => $parts[6]
                    ];
                }
            }
        }
        ?>
        <div class="donation-card" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9; border-radius: 8px;">
            <p><strong>Rice Item:</strong> <?php echo htmlspecialchars($donation['rice_item']); ?></p>
            <p><strong>Curry Item:</strong> <?php echo htmlspecialchars($donation['curry_item']); ?></p>
            <p><strong>Other Item:</strong> <?php echo htmlspecialchars($donation['other_item']); ?></p>
            <p><strong>Serves:</strong> <?php echo $serves; ?> People</p>
            <p><strong>Total Booked:</strong> <?php echo $total_served; ?> People</p>
            <p><strong>Created At:</strong> <?php echo date('d M Y, h:i A', strtotime($donation['created_at'])); ?></p>

            <p><strong>Booking Status:</strong> 
                <?php if ($total_served >= $serves): ?>
                    <span style="color:red; font-weight:bold;">Closed</span>
                <?php else: ?>
                    <span style="color:green; font-weight:bold;">Open</span>
                <?php endif; ?>
            </p>

            <?php if (!empty($bookings)): ?>
                <div style="margin-top: 10px;">
                    <h4>Volunteer Bookings:</h4>
                    <?php foreach ($bookings as $booking): ?>
                        <div class="volunteer-box" style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 8px; border-radius: 5px;">
                            <p><strong>Booked By:</strong> <?php echo htmlspecialchars($booking['volunteer_name']); ?> (ID: <?php echo htmlspecialchars($booking['volunteer_id']); ?>)</p>
                            <p><strong>Mobile:</strong> <?php echo htmlspecialchars($booking['mobile']); ?></p>
                            <p><strong>People Served:</strong> <?php echo (int)$booking['people_served']; ?></p>
                            <p><strong>OTP:</strong> <span class="otp"><?php echo htmlspecialchars($booking['otp']); ?></span></p>
                            <p><strong>Status:</strong>
                                <?php
                                $statusColor = match($booking['status']) {
                                    'Collected' => 'green',
                                    'Pending' => 'orange',
                                    'Cancelled' => 'red',
                                    default => 'black'
                                };
                                ?>
                                <span style="color: <?php echo $statusColor; ?>; font-weight:bold;">
                                    <?php echo htmlspecialchars($booking['status']); ?>
                                </span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: red;">No bookings yet.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
?>
