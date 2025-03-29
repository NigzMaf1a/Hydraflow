<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $paymentID = $_GET['PaymentID'] ?? null;
        $sql = $paymentID ? "SELECT * FROM Payment WHERE PaymentID = ?" : "SELECT * FROM Payment";
        $stmt = $conn->prepare($sql);
        if ($paymentID) $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Payment (ClientID, JobID, PaymentDate, PaymentAmount, PaymentType, ApprovePay, PaymentStatus) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisisss", $data['ClientID'], $data['JobID'], $data['PaymentDate'], $data['PaymentAmount'], $data['PaymentType'], $data['ApprovePay'], $data['PaymentStatus']);
        $stmt->execute();
        echo json_encode(["PaymentID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['PaymentID'])) die(json_encode(["error" => "PaymentID is required"]));
        $sql = "UPDATE Payment SET ClientID=?, JobID=?, PaymentDate=?, PaymentAmount=?, PaymentType=?, ApprovePay=?, PaymentStatus=? WHERE PaymentID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisisssi", $data['ClientID'], $data['JobID'], $data['PaymentDate'], $data['PaymentAmount'], $data['PaymentType'], $data['ApprovePay'], $data['PaymentStatus'], $data['PaymentID']);
        $stmt->execute();
        echo json_encode(["message" => "Payment updated"]);
        break;

    case 'DELETE':
        $paymentID = $_GET['PaymentID'] ?? null;
        if (!$paymentID) die(json_encode(["error" => "PaymentID is required"]));
        $sql = "DELETE FROM Payment WHERE PaymentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        echo json_encode(["message" => "Payment deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>