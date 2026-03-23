<?php
// api/login.php

header('Content-Type: application/json');

require 'vendor/autoload.php'; // Include the Autoloader if using Composer

use Firebase\JWT\JWT;

$host = 'localhost'; // Database host
$db_name = 'your_database_name'; // Database name
$username = 'your_database_user'; // Database username
$password = 'your_database_password'; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['message' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input')); // Get the JSON data sent by the client

if (!isset($data->username) || !isset($data->password)) {
    echo json_encode(['message' => 'Username and password are required']);
    exit;
}

$username = $data->username;
$password = $data->password;

$query = "SELECT * FROM users WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $key = 'your_secret_key'; // Replace with your actual secret key
    $payload = [
        'iat' => time(), // Issued at
        'exp' => time() + (60 * 60), // Expiration time
        'user' => $user['id'] // User ID
    ];
    $jwt = JWT::encode($payload, $key);
    echo json_encode(['token' => $jwt]);
} else {
    echo json_encode(['message' => 'Invalid username or password']);
}