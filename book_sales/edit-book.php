<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: books.php');
    exit;
}

$book = getBook($conn, $_GET['id']);
if (!$book) {
    header('Location: books.php?msg=Book not found&type=danger');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateBook(
        $conn,
        $_GET['id'],
        $_POST['isbn'],
        $_POST['title'],
        $_POST['author'],
        $_POST['category'],
        $_POST['price'],
        $_POST['stock_quantity']
    );
    
    if ($result['success']) {
        header('Location: books.php?msg=' . urlencode($result['message']) . '&type=success');
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">BookStore</a>
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
            <div class="page-header">
                <h1>Edit Book</h1>
                <a href="books.php" class="btn btn-primary">Back to Books</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" 
                               value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($book['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" class="form-control" id="author" name="author" 
                               value="<?php echo htmlspecialchars($book['author']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               value="<?php echo htmlspecialchars($book['category']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                               value="<?php echo $book['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                               value="<?php echo $book['stock_quantity'] ?? 0; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-success">Update Book</button>
                </form>
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