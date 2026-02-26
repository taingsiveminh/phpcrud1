<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$result = processSale($conn, $input['items']);
echo json_encode($result);
?>