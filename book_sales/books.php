<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$books = getBooksWithInventory($conn);

// Handle delete
if (isset($_GET['delete'])) {
    $result = deleteBook($conn, $_GET['delete']);
    if ($result['success']) {
        header('Location: books.php?msg=' . urlencode($result['message']) . '&type=success');
    } else {
        header('Location: books.php?msg=' . urlencode($result['message']) . '&type=danger');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Books - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">📚 BookStore</a>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="books.php" class="active">Books</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="new-sale.php">New Sale</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-<?php echo $_GET['type'] ?? 'info'; ?>">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h1>📖 Books Management</h1>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <input type="text" id="tableSearch" class="search-box" placeholder="🔍 Search books...">
                    <a href="add-book.php" class="btn btn-primary">➕ Add New Book</a>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td data-label="ISBN"><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td data-label="Title"><?php echo htmlspecialchars($book['title']); ?></td>
                                <td data-label="Author"><?php echo htmlspecialchars($book['author']); ?></td>
                                <td data-label="Category"><?php echo htmlspecialchars($book['category']); ?></td>
                                <td data-label="Price">$<?php echo number_format($book['price'], 2); ?></td>
                                <td data-label="Stock"><?php echo $book['stock_quantity'] ?? 0; ?></td>
                                <td data-label="Actions" class="d-flex flex-column flex-md-row gap-1">
                                    <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-warning btn-sm">✏️ Edit</a>
                                    <a href="?delete=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirmDelete('Are you sure you want to delete this book?')">🗑️ Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 BookStore Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>