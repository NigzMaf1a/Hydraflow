<?php
// registration.php - Handles registration-related operations

// Include the database connection
require_once 'connection.php';

// Define the action parameter to determine which operation to perform
$action = $_GET['action'] ?? '';

// Switch between different operations (CRUD)
switch ($action) {
    case 'create':
        createRegistration();
        break;
    case 'read':
        readRegistration();
        break;
    case 'update':
        updateRegistration();
        break;
    case 'delete':
        deleteRegistration();
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action specified."]);
        break;
}

// Create a new registration entry
function createRegistration() {
    global $conn;

    // Collect input data securely using POST request
    $name1 = mysqli_real_escape_string($conn, $_POST['Name1']);
    $name2 = mysqli_real_escape_string($conn, $_POST['Name2']);
    $phoneNo = mysqli_real_escape_string($conn, $_POST['PhoneNo']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT); // Secure password
    $gender = mysqli_real_escape_string($conn, $_POST['Gender']);
    $regType = mysqli_real_escape_string($conn, $_POST['RegType']);
    $dLocation = mysqli_real_escape_string($conn, $_POST['dLocation'] ?? '');
    $accStatus = mysqli_real_escape_string($conn, $_POST['accStatus'] ?? 'Pending');

    // SQL query to insert data into the Registration table
    $sql = "INSERT INTO Registration (Name1, Name2, PhoneNo, Email, Password, Gender, RegType, dLocation, accStatus)
            VALUES ('$name1', '$name2', '$phoneNo', '$email', '$password', '$gender', '$regType', '$dLocation', '$accStatus')";

    if ($conn->query($sql) === TRUE) {
        $regID = $conn->insert_id; // Get the last inserted ID

        // Insert data into corresponding child table based on the registration type
        $balance = 0; // Default balance for all user types
        switch ($regType) {
            case 'Admin':
                $conn->query("INSERT INTO Admin (AdminID, Balance) VALUES ('$regID', '$balance')");
                break;
            case 'Manager':
                $conn->query("INSERT INTO Manager (ManagerID, Balance) VALUES ('$regID', '$balance')");
                break;
            case 'Plumber':
                $conn->query("INSERT INTO Plumber (PlumberID, Balance) VALUES ('$regID', '$balance')");
                break;
            case 'Client':
                $conn->query("INSERT INTO Client (ClientID, Balance) VALUES ('$regID', '$balance')");
                break;
            case 'Mason':
                $conn->query("INSERT INTO Mason (MasonID, Balance) VALUES ('$regID', '$balance')");
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Invalid registration type."]);
                return;
        }

        echo json_encode(["status" => "success", "message" => "Registration created successfully.", "RegID" => $regID]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// Retrieve registration details
function readRegistration() {
    global $conn;

    // Get the RegID from the request
    $regID = $_GET['RegID'] ?? '';

    if ($regID) {
        $sql = "SELECT * FROM Registration WHERE RegID = '$regID'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(["status" => "success", "data" => $row]);
        } else {
            echo json_encode(["status" => "error", "message" => "No record found for RegID: $regID"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "RegID is required."]);
    }
}

// Update registration details
function updateRegistration() {
    global $conn;

    // Collect input data securely from the POST request
    $regID = $_POST['RegID'];
    $name1 = mysqli_real_escape_string($conn, $_POST['Name1']);
    $name2 = mysqli_real_escape_string($conn, $_POST['Name2']);
    $phoneNo = mysqli_real_escape_string($conn, $_POST['PhoneNo']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = isset($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : '';
    $gender = mysqli_real_escape_string($conn, $_POST['Gender']);
    $regType = mysqli_real_escape_string($conn, $_POST['RegType']);
    $dLocation = mysqli_real_escape_string($conn, $_POST['dLocation'] ?? '');
    $accStatus = mysqli_real_escape_string($conn, $_POST['accStatus'] ?? 'Pending');

    // SQL query to update the Registration table
    $sql = "UPDATE Registration SET Name1='$name1', Name2='$name2', PhoneNo='$phoneNo', Email='$email', 
            Password='$password', Gender='$gender', RegType='$regType', dLocation='$dLocation', accStatus='$accStatus' 
            WHERE RegID = '$regID'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Registration updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// Delete a registration entry and related child records
function deleteRegistration() {
    global $conn;

    // Get the RegID from the POST request
    $regID = $_POST['RegID'];

    // Delete child records first (Cascade deletes for each type)
    $conn->query("DELETE FROM Admin WHERE AdminID = '$regID'");
    $conn->query("DELETE FROM Manager WHERE ManagerID = '$regID'");
    $conn->query("DELETE FROM Plumber WHERE PlumberID = '$regID'");
    $conn->query("DELETE FROM Client WHERE ClientID = '$regID'");
    $conn->query("DELETE FROM Mason WHERE MasonID = '$regID'");

    // Delete the registration record
    $sql = "DELETE FROM Registration WHERE RegID = '$regID'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Registration and related records deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// Close the database connection
$conn->close();
?>
