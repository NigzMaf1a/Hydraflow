<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $propertyID = $_GET['PropertyID'] ?? null;
        $sql = $propertyID ? "SELECT * FROM Property WHERE PropertyID = ?" : "SELECT * FROM Property";
        $stmt = $conn->prepare($sql);
        if ($propertyID) $stmt->bind_param("i", $propertyID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Property (ClientID, PropertyName, PropertyType, NumberUnits) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $data['ClientID'], $data['PropertyName'], $data['PropertyType'], $data['NumberUnits']);
        $stmt->execute();
        echo json_encode(["PropertyID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['PropertyID'])) die(json_encode(["error" => "PropertyID is required"]));
        $sql = "UPDATE Property SET ClientID=?, PropertyName=?, PropertyType=?, NumberUnits=? WHERE PropertyID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issii", $data['ClientID'], $data['PropertyName'], $data['PropertyType'], $data['NumberUnits'], $data['PropertyID']);
        $stmt->execute();
        echo json_encode(["message" => "Property updated"]);
        break;

    case 'DELETE':
        $propertyID = $_GET['PropertyID'] ?? null;
        if (!$propertyID) die(json_encode(["error" => "PropertyID is required"]));
        $sql = "DELETE FROM Property WHERE PropertyID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $propertyID);
        $stmt->execute();
        echo json_encode(["message" => "Property deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
