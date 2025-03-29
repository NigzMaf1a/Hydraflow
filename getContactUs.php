<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM Contact";
        $result = $conn->query($sql);
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Contact (PhoneNo, EmailAddress, Instagram, Facebook, POBox) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $data['PhoneNo'], $data['EmailAddress'], $data['Instagram'], $data['Facebook'], $data['POBox']);
        $stmt->execute();
        echo json_encode(["message" => "Contact information added"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "UPDATE Contact SET PhoneNo=?, EmailAddress=?, Instagram=?, Facebook=?, POBox=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $data['PhoneNo'], $data['EmailAddress'], $data['Instagram'], $data['Facebook'], $data['POBox']);
        $stmt->execute();
        echo json_encode(["message" => "Contact information updated"]);
        break;

    case 'DELETE':
        $sql = "DELETE FROM Contact";
        $conn->query($sql);
        echo json_encode(["message" => "All contact information deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
