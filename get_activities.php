<?php
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header("Content-Type: application/json");
require 'db.php'; // Include database connection

try {
    $query = "
        SELECT 
            a.id, a.user_id, a.activity_type, a.activity_text, 
            a.created_at, u.username AS user_name
        FROM activities a
        LEFT JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
    ";
    $result = $conn->query($query);

    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    echo json_encode(['success' => true, 'activities' => $activities]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching activities']);
}
?>
