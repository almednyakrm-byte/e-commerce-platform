<?php
require_once 'db.php';

// Initialize PDO connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Process GET request
if ($method === 'GET') {
    // Validate and sanitize input
    $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if product ID is provided
    if ($productId) {
        // Prepare and execute SQL query to retrieve product by ID
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindParam(':id', $productId);
        $stmt->execute();

        // Fetch product data
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if product exists
        if ($product) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($product);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Product not found']);
        }
    } else {
        // Prepare and execute SQL query to retrieve all products
        $stmt = $pdo->prepare('SELECT * FROM products');
        $stmt->execute();

        // Fetch all products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($products);
    }
}

// Process POST request
elseif ($method === 'POST') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $name = filter_var($input['name'] ?? null, FILTER_SANITIZE_STRING);
    $description = filter_var($input['description'] ?? null, FILTER_SANITIZE_STRING);
    $price = filter_var($input['price'] ?? null, FILTER_VALIDATE_FLOAT);

    // Check if input is valid
    if ($name && $description && $price) {
        // Prepare and execute SQL query to create new product
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price) VALUES (:name, :description, :price)');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->execute();

        // Get last inserted ID
        $productId = $pdo->lastInsertId();

        // Prepare and execute SQL query to retrieve newly created product
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindParam(':id', $productId);
        $stmt->execute();

        // Fetch product data
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
    }
}

// Process PUT request
elseif ($method === 'PUT') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $productId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    $name = filter_var($input['name'] ?? null, FILTER_SANITIZE_STRING);
    $description = filter_var($input['description'] ?? null, FILTER_SANITIZE_STRING);
    $price = filter_var($input['price'] ?? null, FILTER_VALIDATE_FLOAT);

    // Check if input is valid
    if ($productId && $name && $description && $price) {
        // Prepare and execute SQL query to update product
        $stmt = $pdo->prepare('UPDATE products SET name = :name, description = :description, price = :price WHERE id = :id');
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->execute();

        // Prepare and execute SQL query to retrieve updated product
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindParam(':id', $productId);
        $stmt->execute();

        // Fetch product data
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
    }
}

// Process DELETE request
elseif ($method === 'DELETE') {
    // Check if user is admin
    if ($_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input
    $productId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

    // Check if input is valid
    if ($productId) {
        // Prepare and execute SQL query to delete product
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->bindParam(':id', $productId);
        $stmt->execute();

        http_response_code(204);
        header('Content-Type: application/json');
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid input']);
    }
}

// Handle invalid request method
else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
}