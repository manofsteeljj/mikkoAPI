<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Include the database connection
require 'db.php';

try {
    $query = "
        SELECT 
            id, 
            user_id, 
            post_text, 
            post_type, 
            created_at 
        FROM posts 
        ORDER BY created_at DESC
    ";

    $result = $conn->query($query);

    // Check if the query executed successfully
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    // Fetch data into an array
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    // Return JSON response
    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);
} catch (Exception $e) {
    // Handle errors and return failure response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
