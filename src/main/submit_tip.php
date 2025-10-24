<?php
// Start output buffering immediately
ob_start();
session_start();

// Clean any previous output and set JSON header
ob_clean();
header('Content-Type: application/json; charset=utf-8');

// Disable error display (log only)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to share tips.']);
    exit();
}

include 'db_connection.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

// Get form data
$tipTitle = trim($_POST['tip_title'] ?? '');
$tipContent = trim($_POST['tip_content'] ?? '');
$tipCategory = trim($_POST['tip_category'] ?? '');
$userId = $_SESSION['user_id'];

// Validate input
if (empty($tipTitle) || empty($tipContent) || empty($tipCategory)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

// Insert tip into database
$stmt = $conn->prepare("INSERT INTO TIP (tip_title, tip_content, tip_category, user_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $tipTitle, $tipContent, $tipCategory, $userId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Your tip has been shared successfully!',
        'tip_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving tip. Please try again.']);
}

$stmt->close();
$conn->close();
exit();
