<?php
// Start the session to handle user authentication
session_start();

// Import the database connection
require_once 'db.php';

// Check if the request method is GET or POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check the current session status
    if (isset($_SESSION['user_id'])) {
        // User is logged in, return the user ID
        echo json_encode(['status' => 'logged_in', 'user_id' => $_SESSION['user_id']]);
    } else {
        // User is not logged in, return a not logged in status
        echo json_encode(['status' => 'not_logged_in']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action parameter
    if (isset($_POST['action'])) {
        // Handle login action
        if ($_POST['action'] === 'login') {
            // Check if the username and password fields are set
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // Prepare the SQL query to select the user by username
                $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->bind_param('s', $_POST['username']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // Check if the user exists and the password is correct
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    // Set the user ID in the session
                    $_SESSION['user_id'] = $user['id'];
                    echo json_encode(['status' => 'login_success']);
                } else {
                    echo json_encode(['status' => 'login_failed', 'error' => 'Invalid username or password']);
                }
            } else {
                echo json_encode(['status' => 'login_failed', 'error' => 'Username and password are required']);
            }
        } 
        // Handle register action
        elseif ($_POST['action'] === 'register') {
            // Check if the username, email, and password fields are set
            if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
                // Check if the username and email are valid
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    // Prepare the SQL query to insert a new user
                    $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt->bind_param('sss', $_POST['username'], $_POST['email'], $password_hash);
                    if ($stmt->execute()) {
                        // Set the user ID in the session
                        $_SESSION['user_id'] = $db->insert_id;
                        echo json_encode(['status' => 'register_success']);
                    } else {
                        echo json_encode(['status' => 'register_failed', 'error' => 'Failed to insert user into database']);
                    }
                } else {
                    echo json_encode(['status' => 'register_failed', 'error' => 'Invalid email address']);
                }
            } else {
                echo json_encode(['status' => 'register_failed', 'error' => 'Username, email, and password are required']);
            }
        } 
        // Handle logout action
        elseif ($_POST['action'] === 'logout') {
            // Unset the user ID in the session
            unset($_SESSION['user_id']);
            echo json_encode(['status' => 'logout_success']);
        }
    } else {
        echo json_encode(['status' => 'invalid_action', 'error' => 'Action parameter is required']);
    }
} else {
    echo json_encode(['status' => 'invalid_method', 'error' => 'Only GET and POST methods are allowed']);
}