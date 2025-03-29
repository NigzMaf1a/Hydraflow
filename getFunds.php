<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $fundID = $_GET['FundID'] ?? null;
        $sql = $fundID ? "SELECT * FROM Funds WHERE FundID = ?" : "SELECT * FROM Funds";
        $stmt = $conn->prepare($sql);
        if ($fundID) $stmt->bind_param("i", $fundID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Funds (PaymentID, Amount, PaymentDate, Total) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $data['PaymentID'], $data['Amount'], $data['PaymentDate'], $data['Total']);
        $stmt->execute();
        echo json_encode(["FundID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['FundID'])) die(json_encode(["error" => "FundID is required"]));
        $sql = "UPDATE Funds SET PaymentID=?, Amount=?, PaymentDate=?, Total=? WHERE FundID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisii", $data['PaymentID'], $data['Amount'], $data['PaymentDate'], $data['Total'], $data['FundID']);
        $stmt->execute();
        echo json_encode(["message" => "Fund updated"]);
        break;

    case 'DELETE':
        $fundID = $_GET['FundID'] ?? null;
        if (!$fundID) die(json_encode(["error" => "FundID is required"]));
        $sql = "DELETE FROM Funds WHERE FundID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $fundID);
        $stmt->execute();
        echo json_encode(["message" => "Fund deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
