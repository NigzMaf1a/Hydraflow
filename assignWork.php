<?php
// Include the database connection
require_once 'connection.php';

// Set response header to JSON
header('Content-Type: application/json');

// Check the request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Database connection
    $conn = connect();

    switch ($method) {
        case 'GET':
            // Fetch assignments or a specific assignment
            if (isset($_GET['AssignID'])) {
                $stmt = $conn->prepare("SELECT * FROM AssignWork WHERE AssignID = ?");
                $stmt->bind_param("i", $_GET['AssignID']);
            } else {
                $stmt = $conn->prepare("SELECT * FROM AssignWork");
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
            break;

        case 'POST':
            // Add a new assignment
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO AssignWork (ManagerID, JobID, AssignDate, StartDate, EndDate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iisss",
                $input['ManagerID'],
                $input['JobID'],
                $input['AssignDate'],
                $input['StartDate'],
                $input['EndDate']
            );
            $stmt->execute();
            echo json_encode(['status' => 'success', 'AssignID' => $conn->insert_id]);
            break;

        case 'PUT':
            // Update an existing assignment
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['AssignID'])) {
                throw new Exception("AssignID is required for updating an assignment");
            }
            $stmt = $conn->prepare("UPDATE AssignWork SET ManagerID = ?, JobID = ?, AssignDate = ?, StartDate = ?, EndDate = ? WHERE AssignID = ?");
            $stmt->bind_param(
                "iisssi",
                $input['ManagerID'],
                $input['JobID'],
                $input['AssignDate'],
                $input['StartDate'],
                $input['EndDate'],
                $input['AssignID']
            );
            $stmt->execute();
            echo json_encode(['status' => 'success', 'affected_rows' => $stmt->affected_rows]);
            break;

        case 'DELETE':
            // Delete an assignment
            if (!isset($_GET['AssignID'])) {
                throw new Exception("AssignID is required for deleting an assignment");
            }
            $stmt = $conn->prepare("DELETE FROM AssignWork WHERE AssignID = ?");
            $stmt->bind_param("i", $_GET['AssignID']);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'affected_rows' => $stmt->affected_rows]);
            break;

        default:
            // Invalid method
            throw new Exception("Unsupported request method");
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
