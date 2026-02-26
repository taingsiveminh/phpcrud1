<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$books = getBooksWithInventory($conn);
$lowStock = array_filter($books, function($book) {
    return $book['stock_quantity'] < 10;
});

// Get sales stats
$result = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as total, 
                                      MAX(sale_date) as last_sale FROM sales");
$stats = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Dashboard - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">📚 BookStore</a>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="books.php">Books</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="new-sale.php">New Sale</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>📊 Dashboard</h1>
                <a href="new-sale.php" class="btn btn-success">➕ New Sale</a>
            </div>

            <!-- Stats Grid -->
            <div class="grid">
                <div class="stat-card">
                    <h3>Total Books</h3>
                    <div class="stat-value"><?php echo count($books); ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Low Stock Items</h3>
                    <div class="stat-value" style="color: var(--warning-color);">
                        <?php echo count($lowStock); ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>Total Sales</h3>
                    <div class="stat-value"><?php echo $stats['count'] ?? 0; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <div class="stat-value" style="color: var(--success-color);">
                        $<?php echo number_format($stats['total'] ?? 0, 2); ?>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert Section -->
            <h2 class="mt-3 mb-2">⚠️ Low Stock Alert</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lowStock)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">✅ All items are well stocked</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lowStock as $book): ?>
                                <tr>
                                    <td data-label="Title"><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td data-label="Author"><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td data-label="Category"><?php echo htmlspecialchars($book['category']); ?></td>
                                    <td data-label="Stock" style="color: var(--danger-color); font-weight: bold;">
                                        <?php echo $book['stock_quantity']; ?>
                                    </td>
                                    <td data-label="Action">
                                        <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" 
                                           class="btn btn-warning btn-sm">Update Stock</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Sales -->
            <?php
            $recentSales = mysqli_query($conn, "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 5");
            ?>
            <h2 class="mt-3 mb-2">🕒 Recent Sales</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($recentSales) == 0): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No sales yet</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($sale = mysqli_fetch_assoc($recentSales)): ?>
                                <tr>
                                    <td data-label="Sale ID">#<?php echo $sale['sale_id']; ?></td>
                                    <td data-label="Date"><?php echo date('M d, Y H:i', strtotime($sale['sale_date'])); ?></td>
                                    <td data-label="Amount">$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                    <td data-label="Action">
                                        <a href="sale-details.php?id=<?php echo $sale['sale_id']; ?>" 
                                           class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
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