<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $book = getBook($conn, $_GET['id']);
    echo json_encode($book);
} else {
    $books = getBooksWithInventory($conn);
    echo json_encode($books);
}
?>