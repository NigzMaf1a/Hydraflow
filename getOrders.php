<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $orderID = $_GET['OrderID'] ?? null;
        $sql = $orderID ? "SELECT * FROM ClientOrder WHERE OrderID = ?" : "SELECT * FROM ClientOrder";
        $stmt = $conn->prepare($sql);
        if ($orderID) $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO ClientOrder (ClientID, ProductID, OrderDate, Quantity, Price, Paid) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdis", $data['ClientID'], $data['ProductID'], $data['OrderDate'], $data['Quantity'], $data['Price'], $data['Paid']);
        $stmt->execute();
        echo json_encode(["OrderID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['OrderID'])) die(json_encode(["error" => "OrderID is required"]));
        $sql = "UPDATE ClientOrder SET ClientID=?, ProductID=?, OrderDate=?, Quantity=?, Price=?, Paid=? WHERE OrderID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdisi", $data['ClientID'], $data['ProductID'], $data['OrderDate'], $data['Quantity'], $data['Price'], $data['Paid'], $data['OrderID']);
        $stmt->execute();
        echo json_encode(["message" => "Order updated"]);
        break;

    case 'DELETE':
        $orderID = $_GET['OrderID'] ?? null;
        if (!$orderID) die(json_encode(["error" => "OrderID is required"]));
        $sql = "DELETE FROM ClientOrder WHERE OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        echo json_encode(["message" => "Order deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
