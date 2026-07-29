<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) {
    $data = $_POST;
}

// Connect to database
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle GET requests
if ($method == 'GET') {
    // Validate and sanitize input
    $payment_id = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) : null;

    // Prepare SQL query
    if ($payment_id) {
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE id = :id');
        $stmt->bindParam(':id', $payment_id);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM payments');
    }

    // Execute query
    $stmt->execute();

    // Process output
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($payments);
}

// Handle POST requests
elseif ($method == 'POST') {
    // Validate and sanitize input
    $amount = isset($data['amount']) ? filter_var($data['amount'], FILTER_SANITIZE_NUMBER_FLOAT) : null;
    $description = isset($data['description']) ? filter_var($data['description'], FILTER_SANITIZE_STRING) : null;

    // Check for required fields
    if (empty($amount) || empty($description)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Prepare SQL query
    $stmt = $pdo->prepare('INSERT INTO payments (amount, description) VALUES (:amount, :description)');
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);

    // Execute query
    if ($stmt->execute()) {
        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Payment created successfully']);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to create payment']);
    }
}

// Handle PUT requests
elseif ($method == 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Validate and sanitize input
    $payment_id = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) : null;
    $amount = isset($data['amount']) ? filter_var($data['amount'], FILTER_SANITIZE_NUMBER_FLOAT) : null;
    $description = isset($data['description']) ? filter_var($data['description'], FILTER_SANITIZE_STRING) : null;

    // Check for required fields
    if (empty($payment_id) || empty($amount) || empty($description)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Prepare SQL query
    $stmt = $pdo->prepare('UPDATE payments SET amount = :amount, description = :description WHERE id = :id');
    $stmt->bindParam(':id', $payment_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':description', $description);

    // Execute query
    if ($stmt->execute()) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Payment updated successfully']);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to update payment']);
    }
}

// Handle DELETE requests
elseif ($method == 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] != 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Validate and sanitize input
    $payment_id = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) : null;

    // Check for required fields
    if (empty($payment_id)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Prepare SQL query
    $stmt = $pdo->prepare('DELETE FROM payments WHERE id = :id');
    $stmt->bindParam(':id', $payment_id);

    // Execute query
    if ($stmt->execute()) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Payment deleted successfully']);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to delete payment']);
    }
}

// Handle invalid request methods
else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
}