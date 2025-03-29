<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $data = [];
    
    // Get recent registrations
    $sql = "SELECT * FROM Registration ORDER BY lastAccessed DESC LIMIT 5";
    $data['recentRegistrations'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Get recent jobs
    $sql = "SELECT * FROM Job ORDER BY JobID DESC LIMIT 5";
    $data['recentJobs'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Get recent bookings
    $sql = "SELECT * FROM Booking ORDER BY BookingID DESC LIMIT 5";
    $data['recentBookings'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Get recent payments
    $sql = "SELECT * FROM Payment ORDER BY PaymentDate DESC LIMIT 5";
    $data['recentPayments'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Get recent orders
    $sql = "SELECT * FROM ClientOrder ORDER BY OrderDate DESC LIMIT 5";
    $data['recentOrders'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Get recent feedback
    $sql = "SELECT * FROM Feedback ORDER BY FeedbackDate DESC LIMIT 5";
    $data['recentFeedback'] = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
