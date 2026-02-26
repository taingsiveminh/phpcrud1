<?php
include 'db.php';
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: read.php');
    exit;
}

// Handle Confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $connection->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: read.php?msg=deleted');
        exit;
    }
}

// Fetch book info to show confirmation
$stmt = $connection->prepare("SELECT title FROM books WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($title);
if (!$stmt->fetch()) {
    $stmt->close();
    header('Location: read.php');
    exit;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100 text-center">
        <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-trash-alt text-3xl"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Delete Book?</h2>
        <p class="text-gray-500 mb-6">Are you sure you want to remove <span class="font-bold text-gray-900">"<?= htmlspecialchars($title) ?>"</span> from the inventory?</p>

        <form method="POST" class="space-y-3">
            <button type="submit" name="confirm_delete" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                Yes, Delete it
            </button>
            <a href="read.php" class="block w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>