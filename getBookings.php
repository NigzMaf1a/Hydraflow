<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $bookingID = $_GET['BookingID'] ?? null;
        $sql = $bookingID ? "SELECT * FROM Booking WHERE BookingID = ?" : "SELECT * FROM Booking";
        $stmt = $conn->prepare($sql);
        if ($bookingID) $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Booking (JobID, BookDate, Charges, BookType, BookApprove, Completed) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $data['JobID'], $data['BookDate'], $data['Charges'], $data['BookType'], $data['BookApprove'], $data['Completed']);
        $stmt->execute();
        echo json_encode(["BookingID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['BookingID'])) die(json_encode(["error" => "BookingID is required"]));
        $sql = "UPDATE Booking SET JobID=?, BookDate=?, Charges=?, BookType=?, BookApprove=?, Completed=? WHERE BookingID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssi", $data['JobID'], $data['BookDate'], $data['Charges'], $data['BookType'], $data['BookApprove'], $data['Completed'], $data['BookingID']);
        $stmt->execute();
        echo json_encode(["message" => "Booking updated"]);
        break;

    case 'DELETE':
        $bookingID = $_GET['BookingID'] ?? null;
        if (!$bookingID) die(json_encode(["error" => "BookingID is required"]));
        $sql = "DELETE FROM Booking WHERE BookingID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        echo json_encode(["message" => "Booking deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
