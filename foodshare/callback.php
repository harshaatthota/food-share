<?php
include 'vendor/autoload.php';

$apiKey = 'Gx7GOJgyRezFzT9Iw0PYSQtrgO7STVr6WF0L7ONh4u8JBjLzmlDwSzjewrtxd9aGMYYtQwqPMRjIz9i5pwWsgGVj9R8l2ag1nAee4PLTp9t6mjZc8VLxsKJHMMv2cbnh';
$apiSecret = '2zthD1M9W3OBsRlvyYS0JqZAt7MsvRWrQKqW72ulnWCWHBNFd5TxnDoTkYSclUt0Tb51Nw0mDfEeKeaRwtfp3MrAN2QEipAJM4WU1p0YtPXS4O2iUbob4Ewb2K1S3ECs';

$razorpay = new Razorpay\Api\Api($apiKey, $apiSecret);

try {
    // Verify payment signature
    $attributes = [
        'razorpay_order_id' => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
        'razorpay_signature' => $_POST['razorpay_signature']
    ];
    
    $razorpay->utility->verifyPaymentSignature($attributes);
    
    // Fetch order details
    $order = $razorpay->order->fetch($_POST['razorpay_order_id']);
    $notes = $order['notes'];
    
    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'food_share');
    
    // Sanitize data from notes
    $name = $conn->real_escape_string($notes['contributor_name']);
    $amount = $conn->real_escape_string($notes['contribution_amount']);
    $payment_method = $conn->real_escape_string($notes['payment_method']);
    $card_number = $conn->real_escape_string($notes['card_number']);
    
    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO contributions (name, amount, payment_method, card_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $amount, $payment_method, $card_number);

    if ($stmt->execute()) {
        echo "<script>alert('Payment successful!'); window.location.href='index.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "Payment failed: " . $e->getMessage();
}
?>