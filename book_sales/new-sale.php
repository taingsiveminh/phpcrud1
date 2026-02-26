<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$books = getBooksWithInventory($conn);
$availableBooks = array_filter($books, function($book) {
    return $book['stock_quantity'] > 0;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>New Sale - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">📚 BookStore</a>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="books.php">Books</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="new-sale.php" class="active">New Sale</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>🛒 New Sale</h1>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="sales.php" class="btn btn-primary">📋 View Sales</a>
                </div>
            </div>

            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 class="mb-2">Add Items to Sale</h3>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <div class="form-group" style="flex: 2; margin-bottom: 0;">
                        <label for="bookSelect" class="mb-1">Select Book</label>
                        <select id="bookSelect" class="form-control">
                            <option value="">Choose a book...</option>
                            <?php foreach ($availableBooks as $book): ?>
                                <option value="<?php echo $book['book_id']; ?>" 
                                        data-price="<?php echo $book['price']; ?>"
                                        data-stock="<?php echo $book['stock_quantity']; ?>">
                                    <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author']); ?> 
                                    - $<?php echo $book['price']; ?> (Stock: <?php echo $book['stock_quantity']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="itemQuantity" class="mb-1">Quantity</label>
                        <input type="number" id="itemQuantity" class="form-control" value="1" min="1">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="button" id="addItem" class="btn btn-success" style="width: 100%;">
                            ➕ Add Item
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="mb-3">🛍️ Sale Items</h3>
                <form id="saleForm">
                    <div id="itemsContainer" class="sale-items">
                        <!-- Items will be added here dynamically -->
                        <p class="text-center" style="color: #7f8c8d; padding: 2rem;">
                            No items added yet. Select a book above to start.
                        </p>
                    </div>
                    
                    <div class="d-flex justify-between align-center mt-3" style="flex-wrap: wrap; gap: 1rem;">
                        <strong style="font-size: 1.2rem;">Total: <span id="totalAmount">$0.00</span></strong>
                        <button type="submit" class="btn btn-success" style="min-width: 200px;">
                            ✅ Complete Sale
                        </button>
                    </div>
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