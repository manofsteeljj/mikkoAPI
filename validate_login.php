<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow requests from localhost:3000
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow headers

// For handling preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Stop processing if it's an OPTIONS request (preflight)
}

// Start the session
session_start();

// Include database connection
require_once 'db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Only POST requests are allowed."]);
    exit;
}

// Get the input data
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['email'], $input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Email and password are required."]);
    exit;
}

$email = $input['email'];
$password = $input['password'];

// Validate input
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Invalid email format."]);
    exit;
}


// Query to check if the user exists
$query = "SELECT id, password FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401); // Unauthorized
    echo json_encode(["message" => "Invalid email or password."]);
    exit;
}

// Fetch user data
$user = $result->fetch_assoc();

// Verify the password
if (!password_verify($password, $user['password'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["message" => "Invalid email or password."]);
    exit;
}

// Set session variables for the authenticated user
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $email;

// Respond with success
http_response_code(200); // OK
echo json_encode(["message" => "Login successful.", "user_id" => $user['id']]);

?>
