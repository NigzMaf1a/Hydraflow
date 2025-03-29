<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $toolID = $_GET['ToolID'] ?? null;
        $sql = $toolID ? "SELECT * FROM Tools WHERE ToolID = ?" : "SELECT * FROM Tools";
        $stmt = $conn->prepare($sql);
        if ($toolID) $stmt->bind_param("i", $toolID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Tools (ToolName, ToolDescription, ToolCondition, Price, ToolUnits, Available) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdis", $data['ToolName'], $data['ToolDescription'], $data['ToolCondition'], $data['Price'], $data['ToolUnits'], $data['Available']);
        $stmt->execute();
        echo json_encode(["ToolID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['ToolID'])) die(json_encode(["error" => "ToolID is required"]));
        $sql = "UPDATE Tools SET ToolName=?, ToolDescription=?, ToolCondition=?, Price=?, ToolUnits=?, Available=? WHERE ToolID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdisi", $data['ToolName'], $data['ToolDescription'], $data['ToolCondition'], $data['Price'], $data['ToolUnits'], $data['Available'], $data['ToolID']);
        $stmt->execute();
        echo json_encode(["message" => "Tool updated"]);
        break;

    case 'DELETE':
        $toolID = $_GET['ToolID'] ?? null;
        if (!$toolID) die(json_encode(["error" => "ToolID is required"]));
        $sql = "DELETE FROM Tools WHERE ToolID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $toolID);
        $stmt->execute();
        echo json_encode(["message" => "Tool deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
