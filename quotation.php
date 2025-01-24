<?php
require_once 'connection.php';

header('Content-Type: application/json');

$response = ["status" => "error", "message" => "Invalid request."];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operation'])) {
    $operation = $_POST['operation'];

    switch ($operation) {
        case 'create':
            if (isset($_POST['ClientID'], $_POST['JobID'], $_POST['QuotationDate'], $_POST['QuotationAmount'])) {
                $clientID = intval($_POST['ClientID']);
                $jobID = intval($_POST['JobID']);
                $quotationDate = $_POST['QuotationDate'];
                $quotationAmount = intval($_POST['QuotationAmount']);

                $query = "INSERT INTO Quotation (ClientID, JobID, QuotationDate, QuotationAmount) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iisi', $clientID, $jobID, $quotationDate, $quotationAmount);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Quotation created successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error creating quotation."];
                }
                $stmt->close();
            }
            break;

        case 'read':
            $query = "SELECT * FROM Quotation";
            $result = $conn->query($query);

            if ($result) {
                $quotations = $result->fetch_all(MYSQLI_ASSOC);
                $response = ["status" => "success", "data" => $quotations];
            } else {
                $response = ["status" => "error", "message" => "Error fetching quotations."];
            }
            break;

        case 'update':
            if (isset($_POST['QuotationID'], $_POST['Status'])) {
                $quotationID = intval($_POST['QuotationID']);
                $status = $_POST['Status'];

                $query = "UPDATE Quotation SET Status = ? WHERE QuotationID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('si', $status, $quotationID);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Quotation updated successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error updating quotation."];
                }
                $stmt->close();
            }
            break;

        case 'delete':
            if (isset($_POST['QuotationID'])) {
                $quotationID = intval($_POST['QuotationID']);

                $query = "DELETE FROM Quotation WHERE QuotationID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $quotationID);

                if ($stmt->execute()) {
                    $response = ["status" => "success", "message" => "Quotation deleted successfully."];
                } else {
                    $response = ["status" => "error", "message" => "Error deleting quotation."];
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
