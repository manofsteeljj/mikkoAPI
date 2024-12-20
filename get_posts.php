<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header("Content-Type: application/json");
require 'db.php'; // Include database connection

try {
    $query = "
        SELECT 
            p.id, p.user_id, p.post_text, p.post_image, p.post_video, p.post_type, 
            p.created_at, u.username AS user_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ";
    $result = $conn->query($query);

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching posts']);
}
?>
