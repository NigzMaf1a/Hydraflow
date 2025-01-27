<?php
// Include the database connection
include('connection.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Get the HTTP method used for the request
$method = $_SERVER['REQUEST_METHOD'];

// Handle different types of requests
switch($method) {
    case 'GET':
        // Get FAQ(s) (Retrieve data)
        if (isset($_GET['FAQID'])) {
            getFAQ($_GET['FAQID']);
        } else {
            getAllFAQs();
        }
        break;
        
    case 'POST':
        // Create a new FAQ
        createFAQ();
        break;
        
    case 'PUT':
        // Update an existing FAQ
        updateFAQ();
        break;
        
    case 'DELETE':
        // Delete an FAQ
        deleteFAQ();
        break;
        
    default:
        // Invalid method
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Function to get all FAQs
function getAllFAQs() {
    global $conn;
    $sql = "SELECT * FROM FAQs";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $faqs = [];
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
        echo json_encode($faqs);
    } else {
        echo json_encode(["message" => "No FAQs found"]);
    }
}

// Function to get a single FAQ by ID
function getFAQ($faqID) {
    global $conn;
    $sql = "SELECT * FROM FAQs WHERE FAQID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $faqID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $faq = $result->fetch_assoc();
        echo json_encode($faq);
    } else {
        echo json_encode(["message" => "FAQ not found"]);
    }
}

// Function to create a new FAQ
function createFAQ() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->Question) && isset($data->Answer)) {
        $question = $data->Question;
        $answer = $data->Answer;
        
        $sql = "INSERT INTO FAQs (Question, Answer) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $question, $answer);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "FAQ created successfully", "FAQID" => $conn->insert_id]);
        } else {
            echo json_encode(["message" => "Error creating FAQ"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to update an existing FAQ
function updateFAQ() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->FAQID) && isset($data->Question) && isset($data->Answer)) {
        $faqID = $data->FAQID;
        $question = $data->Question;
        $answer = $data->Answer;
        
        $sql = "UPDATE FAQs SET Question = ?, Answer = ? WHERE FAQID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $question, $answer, $faqID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "FAQ updated successfully"]);
        } else {
            echo json_encode(["message" => "Error updating FAQ"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to delete an FAQ
function deleteFAQ() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->FAQID)) {
        $faqID = $data->FAQID;
        
        $sql = "DELETE FROM FAQs WHERE FAQID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $faqID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "FAQ deleted successfully"]);
        } else {
            echo json_encode(["message" => "Error deleting FAQ"]);
        }
    } else {
        echo json_encode(["message" => "FAQID is required"]);
    }
}
?>
