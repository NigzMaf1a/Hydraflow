<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $feedbackID = $_GET['FeedbackID'] ?? null;
        $sql = $feedbackID ? "SELECT * FROM Feedback WHERE FeedbackID = ?" : "SELECT * FROM Feedback";
        $stmt = $conn->prepare($sql);
        if ($feedbackID) $stmt->bind_param("i", $feedbackID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO Feedback (RegID, FirstName, LastName, FeedbackText, Comments, Response, Rating) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssi", $data['RegID'], $data['FirstName'], $data['LastName'], $data['FeedbackText'], $data['Comments'], $data['Response'], $data['Rating']);
        $stmt->execute();
        echo json_encode(["FeedbackID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['FeedbackID'])) die(json_encode(["error" => "FeedbackID is required"]));
        $sql = "UPDATE Feedback SET RegID=?, FirstName=?, LastName=?, FeedbackText=?, Comments=?, Response=?, Rating=? WHERE FeedbackID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssii", $data['RegID'], $data['FirstName'], $data['LastName'], $data['FeedbackText'], $data['Comments'], $data['Response'], $data['Rating'], $data['FeedbackID']);
        $stmt->execute();
        echo json_encode(["message" => "Feedback updated"]);
        break;

    case 'DELETE':
        $feedbackID = $_GET['FeedbackID'] ?? null;
        if (!$feedbackID) die(json_encode(["error" => "FeedbackID is required"]));
        $sql = "DELETE FROM Feedback WHERE FeedbackID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $feedbackID);
        $stmt->execute();
        echo json_encode(["message" => "Feedback deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
