<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Stop processing if it's an OPTIONS request (preflight)
}

// Include database connection
require_once 'db.php';

// Get the input data
$input = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($input['name'], $input['email'], $input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Name, email, and password are required."]);
    exit;
}

$name = $input['name'];
$email = $input['email'];
$password = $input['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Invalid email format."]);
    exit;
}

// Check if the email already exists
$query = "SELECT id FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["message" => "Email already exists."]);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert the user into the database with default values for reset_token, token_expiry, role, and status
$query = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'member', 'active')";
$stmt = $conn->prepare($query);
$stmt->bind_param('sss', $name, $email, $hashedPassword);

if ($stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode(["message" => "User registered successfully."]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "An error occurred during registration."]);
}

$stmt->close();
$conn->close();
?>
