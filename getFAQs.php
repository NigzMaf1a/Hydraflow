<?php

header("Content-Type: application/json");
require 'connection.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $faqID = $_GET['FAQID'] ?? null;
        $sql = $faqID ? "SELECT * FROM FAQs WHERE FAQID = ?" : "SELECT * FROM FAQs";
        $stmt = $conn->prepare($sql);
        if ($faqID) $stmt->bind_param("i", $faqID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $sql = "INSERT INTO FAQs (Question, Answer) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $data['Question'], $data['Answer']);
        $stmt->execute();
        echo json_encode(["FAQID" => $stmt->insert_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['FAQID'])) die(json_encode(["error" => "FAQID is required"]));
        $sql = "UPDATE FAQs SET Question=?, Answer=? WHERE FAQID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $data['Question'], $data['Answer'], $data['FAQID']);
        $stmt->execute();
        echo json_encode(["message" => "FAQ updated"]);
        break;

    case 'DELETE':
        $faqID = $_GET['FAQID'] ?? null;
        if (!$faqID) die(json_encode(["error" => "FAQID is required"]));
        $sql = "DELETE FROM FAQs WHERE FAQID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $faqID);
        $stmt->execute();
        echo json_encode(["message" => "FAQ deleted"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
