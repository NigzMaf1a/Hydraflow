<?php
require_once 'connection.php'; // Include the database connection

header('Content-Type: application/json');

$response = ["status" => "error", "message" => "Invalid request."];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operation'])) {
    $operation = $_POST['operation'];

    switch ($operation) {
        case 'create':
            if (isset($_POST['JobID'], $_POST['BookDate'], $_POST['Charges'], $_POST['BookType'])) {
                $jobID = intval($_POST['JobID']);
                $bookDate = $_POST['BookDate'];
                $charges = intval($_POST['Charges']);
                $bookType = $_POST['BookType'];

                $query = "INSERT INTO Booking (JobID, BookDate, Charges, BookType) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('isis', $jobID, $bookDate, $charges, $bookType);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Booking created successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error creating booking."];
                }
                $stmt->close();
            }
            break;

        case 'read':
            if (isset($_POST['JobID'])) {
                $jobID = intval($_POST['JobID']);

                $query = "SELECT * FROM Booking WHERE JobID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $jobID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result) {
                    $bookings = $result->fetch_all(MYSQLI_ASSOC);
                    $response = ["status" => "success", "data" => $bookings];
                } else {
                    $response = ["status" => "error", "message" => "Error fetching bookings."];
                }
                $stmt->close();
            }
            break;

        case 'update':
            if (isset($_POST['BookingID'], $_POST['BookApprove'], $_POST['Completed'])) {
                $bookingID = intval($_POST['BookingID']);
                $bookApprove = $_POST['BookApprove'];
                $completed = $_POST['Completed'];

                $query = "UPDATE Booking SET BookApprove = ?, Completed = ? WHERE BookingID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ssi', $bookApprove, $completed, $bookingID);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Booking updated successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error updating booking."];
                }
                $stmt->close();
            }
            break;

        case 'delete':
            if (isset($_POST['BookingID'])) {
                $bookingID = intval($_POST['BookingID']);

                $query = "DELETE FROM Booking WHERE BookingID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $bookingID);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Booking deleted successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error deleting booking."];
                }
                $stmt->close();
            }
            break;

        default:
            $response = ["status" => "error", "message" => "Unsupported operation."];
            break;
    }
}

$conn->close();

echo json_encode($response);
?>
