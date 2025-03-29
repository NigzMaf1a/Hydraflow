<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $productID = $_GET['ProductID'] ?? null;
        $sql = $productID ? "SELECT * FROM Product WHERE ProductID = ?" : "SELECT * FROM Product";
        $stmt = $conn->prepare($sql);
        if ($productID) $stmt->bind_param("i", $productID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Product (ProductName, ProductDescription, Price, ProductUnits, ProductImage, Available) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdis", $data['ProductName'], $data['ProductDescription'], $data['Price'], $data['ProductUnits'], $data['ProductImage'], $data['Available']);
        $stmt->execute();
        echo json_encode(["ProductID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['ProductID'])) die(json_encode(["error" => "ProductID is required"]));
        $sql = "UPDATE Product SET ProductName=?, ProductDescription=?, Price=?, ProductUnits=?, ProductImage=?, Available=? WHERE ProductID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi", $data['ProductName'], $data['ProductDescription'], $data['Price'], $data['ProductUnits'], $data['ProductImage'], $data['Available'], $data['ProductID']);
        $stmt->execute();
        echo json_encode(["message" => "Product updated"]);
        break;

    case 'DELETE':
        $productID = $_GET['ProductID'] ?? null;
        if (!$productID) die(json_encode(["error" => "ProductID is required"]));
        $sql = "DELETE FROM Product WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        echo json_encode(["message" => "Product deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
