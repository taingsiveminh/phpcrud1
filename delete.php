<?php
include 'db.php';
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: read.php?msg=deleted');
exit;
?>