<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header("Content-Type: application/json");
require 'db.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['post_id'])) {
    $post_id = (int)$data['post_id'];

    try {
        $query = "DELETE FROM posts WHERE id = $post_id";
        $result = $conn->query($query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
