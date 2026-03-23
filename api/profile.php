<?php
session_start();

// Include the necessary files for JWT handling
require "vendor/autoload.php";
use \\Firebase\JWT\JWT;

// Set your secret key
$secretKey = 'YOUR_SECRET_KEY';

// Function to authenticate the user based on the JWT token
function authenticate() {
    global $secretKey;
    
    // Get the JWT token from the Authorization header
    $headers = getallheaders();
    if(isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        // Decode the token
        try {
            $decoded = JWT::decode($token, $secretKey, array('HS256'));
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized.']);
            exit();
        }
    }
    http_response_code(401);
    echo json_encode(['message' => 'Authorization header not found.']);
    exit();
}

// Authenticate the user
$user = authenticate();

// Return user profile as JSON
header('Content-Type: application/json');
echo json_encode(['id' => $user->id, 'username' => $user->username]);
?>