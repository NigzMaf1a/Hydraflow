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
        // Get properties (Retrieve data)
        if (isset($_GET['PropertyID'])) {
            getProperty($_GET['PropertyID']);
        } else {
            getAllProperties();
        }
        break;
        
    case 'POST':
        // Create a new property
        createProperty();
        break;
        
    case 'PUT':
        // Update a property
        updateProperty();
        break;
        
    case 'DELETE':
        // Delete a property
        deleteProperty();
        break;
        
    default:
        // Invalid method
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Function to get all properties
function getAllProperties() {
    global $conn;
    $sql = "SELECT * FROM Property";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        echo json_encode($properties);
    } else {
        echo json_encode(["message" => "No properties found"]);
    }
}

// Function to get a single property by ID
function getProperty($propertyID) {
    global $conn;
    $sql = "SELECT * FROM Property WHERE PropertyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $propertyID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        echo json_encode($property);
    } else {
        echo json_encode(["message" => "Property not found"]);
    }
}

// Function to create a new property
function createProperty() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->ClientID) && isset($data->PropertyName) && isset($data->PropertyType)) {
        $clientID = $data->ClientID;
        $propertyName = $data->PropertyName;
        $propertyType = $data->PropertyType;
        $numberUnits = isset($data->NumberUnits) ? $data->NumberUnits : null;
        
        $sql = "INSERT INTO Property (ClientID, PropertyName, PropertyType, NumberUnits) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $clientID, $propertyName, $propertyType, $numberUnits);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Property created successfully", "PropertyID" => $conn->insert_id]);
        } else {
            echo json_encode(["message" => "Error creating property"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to update an existing property
function updateProperty() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->PropertyID) && isset($data->ClientID) && isset($data->PropertyName) && isset($data->PropertyType)) {
        $propertyID = $data->PropertyID;
        $clientID = $data->ClientID;
        $propertyName = $data->PropertyName;
        $propertyType = $data->PropertyType;
        $numberUnits = isset($data->NumberUnits) ? $data->NumberUnits : null;
        
        $sql = "UPDATE Property SET ClientID = ?, PropertyName = ?, PropertyType = ?, NumberUnits = ? WHERE PropertyID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $clientID, $propertyName, $propertyType, $numberUnits, $propertyID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Property updated successfully"]);
        } else {
            echo json_encode(["message" => "Error updating property"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to delete a property
function deleteProperty() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->PropertyID)) {
        $propertyID = $data->PropertyID;
        
        $sql = "DELETE FROM Property WHERE PropertyID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $propertyID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Property deleted successfully"]);
        } else {
            echo json_encode(["message" => "Error deleting property"]);
        }
    } else {
        echo json_encode(["message" => "PropertyID is required"]);
    }
}
?>
