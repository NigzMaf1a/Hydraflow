<?php
// Include the database connection
include('connection.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Get the HTTP method used for the request
$method = $_SERVER['REQUEST_METHOD'];

// Handle different requests
switch($method) {
    case 'GET':
        // Get unit(s) (Retrieve data)
        if (isset($_GET['UnitID'])) {
            getUnit($_GET['UnitID']);
        } else {
            getAllUnits();
        }
        break;
        
    case 'POST':
        // Create a new unit
        createUnit();
        break;
        
    case 'PUT':
        // Update an existing unit
        updateUnit();
        break;
        
    case 'DELETE':
        // Delete a unit
        deleteUnit();
        break;
        
    default:
        // Invalid method
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Function to get all units
function getAllUnits() {
    global $conn;
    $sql = "SELECT * FROM Unit";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
        echo json_encode($units);
    } else {
        echo json_encode(["message" => "No units found"]);
    }
}

// Function to get a single unit by ID
function getUnit($unitID) {
    global $conn;
    $sql = "SELECT * FROM Unit WHERE UnitID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $unitID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $unit = $result->fetch_assoc();
        echo json_encode($unit);
    } else {
        echo json_encode(["message" => "Unit not found"]);
    }
}

// Function to create a new unit
function createUnit() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->PropertyID) && isset($data->UnitName)) {
        $propertyID = $data->PropertyID;
        $unitName = $data->UnitName;
        
        $sql = "INSERT INTO Unit (PropertyID, UnitName) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $propertyID, $unitName);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Unit created successfully", "UnitID" => $conn->insert_id]);
        } else {
            echo json_encode(["message" => "Error creating unit"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to update an existing unit
function updateUnit() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->UnitID) && isset($data->PropertyID) && isset($data->UnitName)) {
        $unitID = $data->UnitID;
        $propertyID = $data->PropertyID;
        $unitName = $data->UnitName;
        
        $sql = "UPDATE Unit SET PropertyID = ?, UnitName = ? WHERE UnitID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $propertyID, $unitName, $unitID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Unit updated successfully"]);
        } else {
            echo json_encode(["message" => "Error updating unit"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to delete a unit from a property
function deleteUnit() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->UnitID)) {
        $unitID = $data->UnitID;
        
        $sql = "DELETE FROM Unit WHERE UnitID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $unitID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Unit deleted successfully"]);
        } else {
            echo json_encode(["message" => "Error deleting unit"]);
        }
    } else {
        echo json_encode(["message" => "UnitID is required"]);
    }
}
?>
