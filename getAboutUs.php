<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM About";
        $result = $conn->query($sql);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO About (Detail) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $data['Detail']);
        $stmt->execute();
        echo json_encode(["message" => "About information added"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "UPDATE About SET Detail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $data['Detail']);
        $stmt->execute();
        echo json_encode(["message" => "About information updated"]);
        break;

    case 'DELETE':
        $sql = "DELETE FROM About";
        $conn->query($sql);
        echo json_encode(["message" => "All About information deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
