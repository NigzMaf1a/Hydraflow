<?php
session_start();

// Ensure user is logged in and approved
if (!isset($_SESSION['user_id']) || $_SESSION['accStatus'] !== 'Approved') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "username", "password", "database");

if ($mysqli->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read all records
        $result = $mysqli->query("SELECT * FROM Booking");
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($bookings);
        break;

    case 'POST':
        // Create a new booking
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("INSERT INTO Booking (JobID, BookDate, Charges, BookType) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $data['JobID'], $data['BookDate'], $data['Charges'], $data['BookType']);
        echo json_encode(["success" => $stmt->execute()]);
        break;

    case 'PUT':
        // Update an existing booking
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("UPDATE Booking SET JobID=?, BookDate=?, Charges=?, BookType=?, BookApprove=?, Completed=? WHERE BookingID=?");
        $stmt->bind_param("isisssi", $data['JobID'], $data['BookDate'], $data['Charges'], $data['BookType'], $data['BookApprove'], $data['Completed'], $data['BookingID']);
        echo json_encode(["success" => $stmt->execute()]);
        break;

    case 'DELETE':
        // Delete a booking
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("DELETE FROM Booking WHERE BookingID=?");
        $stmt->bind_param("i", $data['BookingID']);
        echo json_encode(["success" => $stmt->execute()]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

$mysqli->close();
?>
