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
        // Get order(s) (Retrieve data)
        if (isset($_GET['OrderID'])) {
            getOrder($_GET['OrderID']);
        } else {
            getAllOrders();
        }
        break;
        
    case 'POST':
        // Create a new order
        createOrder();
        break;
        
    case 'PUT':
        // Update an existing order
        updateOrder();
        break;
        
    case 'DELETE':
        // Delete an order
        deleteOrder();
        break;
        
    default:
        // Invalid method
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Function to get all orders
function getAllOrders() {
    global $conn;
    $sql = "SELECT * FROM ClientOrder";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode($orders);
    } else {
        echo json_encode(["message" => "No orders found"]);
    }
}

// Function to get a single order by ID
function getOrder($orderID) {
    global $conn;
    $sql = "SELECT * FROM ClientOrder WHERE OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode($order);
    } else {
        echo json_encode(["message" => "Order not found"]);
    }
}

// Function to create a new order
function createOrder() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->ClientID) && isset($data->ProductID) && isset($data->OrderDate) && isset($data->Quantity) && isset($data->Price)) {
        $clientID = $data->ClientID;
        $productID = $data->ProductID;
        $orderDate = $data->OrderDate;
        $quantity = $data->Quantity;
        $price = $data->Price;
        $paid = $data->Paid ?? 'NO';
        
        $sql = "INSERT INTO ClientOrder (ClientID, ProductID, OrderDate, Quantity, Price, Paid) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdis", $clientID, $productID, $orderDate, $quantity, $price, $paid);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Order created successfully", "OrderID" => $conn->insert_id]);
        } else {
            echo json_encode(["message" => "Error creating order"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to update an existing order
function updateOrder() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->OrderID) && isset($data->ClientID) && isset($data->ProductID) && isset($data->OrderDate) && isset($data->Quantity) && isset($data->Price)) {
        $orderID = $data->OrderID;
        $clientID = $data->ClientID;
        $productID = $data->ProductID;
        $orderDate = $data->OrderDate;
        $quantity = $data->Quantity;
        $price = $data->Price;
        $paid = $data->Paid ?? 'NO';
        
        $sql = "UPDATE ClientOrder SET ClientID = ?, ProductID = ?, OrderDate = ?, Quantity = ?, Price = ?, Paid = ? WHERE OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdisi", $clientID, $productID, $orderDate, $quantity, $price, $paid, $orderID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Order updated successfully"]);
        } else {
            echo json_encode(["message" => "Error updating order"]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields"]);
    }
}

// Function to delete an order
function deleteOrder() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->OrderID)) {
        $orderID = $data->OrderID;
        
        $sql = "DELETE FROM ClientOrder WHERE OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderID);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Order deleted successfully"]);
        } else {
            echo json_encode(["message" => "Error deleting order"]);
        }
    } else {
        echo json_encode(["message" => "OrderID is required"]);
    }
}
?>
