<?php
// Import database connection file
require_once 'db.php';

// Initialize database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate and sanitize input
    $orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if order ID is provided
    if ($orderId) {
        // Retrieve order by ID
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        $order = $stmt->fetch();

        // Check if order exists
        if ($order) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($order);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Order not found']);
        }
    } else {
        // Retrieve all orders
        $stmt = $pdo->prepare('SELECT * FROM orders');
        $stmt->execute();
        $orders = $stmt->fetchAll();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($orders);
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $customerId = filter_var($data['customer_id'] ?? null, FILTER_VALIDATE_INT);
    $orderDate = filter_var($data['order_date'] ?? null, FILTER_VALIDATE_DATE);
    $total = filter_var($data['total'] ?? null, FILTER_VALIDATE_FLOAT);

    // Check if required fields are provided
    if ($customerId && $orderDate && $total) {
        // Insert new order
        $stmt = $pdo->prepare('INSERT INTO orders (customer_id, order_date, total) VALUES (:customer_id, :order_date, :total)');
        $stmt->bindParam(':customer_id', $customerId);
        $stmt->bindParam(':order_date', $orderDate);
        $stmt->bindParam(':total', $total);
        $stmt->execute();

        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Order created successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid request data']);
    }
}

// Handle PUT requests
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $orderId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
    $customerId = filter_var($data['customer_id'] ?? null, FILTER_VALIDATE_INT);
    $orderDate = filter_var($data['order_date'] ?? null, FILTER_VALIDATE_DATE);
    $total = filter_var($data['total'] ?? null, FILTER_VALIDATE_FLOAT);

    // Check if required fields are provided
    if ($orderId && $customerId && $orderDate && $total) {
        // Update order
        $stmt = $pdo->prepare('UPDATE orders SET customer_id = :customer_id, order_date = :order_date, total = :total WHERE id = :id');
        $stmt->bindParam(':id', $orderId);
        $stmt->bindParam(':customer_id', $customerId);
        $stmt->bindParam(':order_date', $orderDate);
        $stmt->bindParam(':total', $total);
        $stmt->execute();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Order updated successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid request data']);
    }
}

// Handle DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get input data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $orderId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if order ID is provided
    if ($orderId) {
        // Delete order
        $stmt = $pdo->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Order deleted successfully']);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid request data']);
    }
}