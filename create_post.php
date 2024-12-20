<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
header("Content-Type: application/json");
require 'db.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['post_text']) && isset($data['post_type'])) {
    $post_text = $conn->real_escape_string($data['post_text']);
    $post_type = $conn->real_escape_string($data['post_type']);
    $user_id = $data['user_id']; // Replace with session user if applicable
    $post_image = isset($data['post_image']) ? $conn->real_escape_string($data['post_image']) : NULL;
    $post_video = isset($data['post_video']) ? $conn->real_escape_string($data['post_video']) : NULL;

    try {
        $query = "
            INSERT INTO posts (user_id, post_text, post_image, post_video, post_type)
            VALUES ('$user_id', '$post_text', '$post_image', '$post_video', '$post_type')
        ";
        $result = $conn->query($query);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Post created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create post']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error creating post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
