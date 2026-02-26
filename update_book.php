<?php
include 'db.php';

$id = intval($_GET['id'] ?? 0);
$error = "";
$success = false;

// Fetch existing book data
if ($id > 0) {
    $stmt = $connection->prepare("SELECT title, author, price, stock FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if (!$book) {
        header("Location: read.php");
        exit;
    }
} else {
    header("Location: read.php");
    exit;
}

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    if (empty($title) || empty($author)) {
        $error = "All fields are required.";
    } else {
        $stmt = $connection->prepare("UPDATE books SET title = ?, author = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("ssdii", $title, $author, $price, $stock, $id);
        
        if ($stmt->execute()) {
            header("Location: read.php?msg=updated");
            exit;
        } else {
            $error = "Update failed: " . $connection->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-edit text-blue-600 mr-3"></i>Edit Book
        </h2>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($book['title']) ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <input type="text" name="author" required value="<?= htmlspecialchars($book['author']) ?>" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                    <input type="number" step="0.01" name="price" required value="<?= $book['price'] ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" required value="<?= $book['stock'] ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <div class="pt-6 flex items-center justify-between">
                <a href="read.php" class="text-sm text-gray-500 hover:underline">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition-all">
                    Update Book
                </button>
            </div>
        </form>
    </div>
</body>
</html>