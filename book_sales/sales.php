<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$sales = getSalesHistory($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">BookStore</a>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="books.php">Books</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="sales.php" class="active">Sales</a></li>
                <li><a href="new-sale.php">New Sale</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Sales History</h1>
                <a href="new-sale.php" class="btn btn-primary">New Sale</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No sales yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td>#<?php echo $sale['sale_id']; ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($sale['sale_date'])); ?></td>
                                    <td><?php echo $sale['item_count']; ?></td>
                                    <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                    <td>
                                        <a href="sale-details.php?id=<?php echo $sale['sale_id']; ?>" 
                                           class="btn btn-primary btn-sm">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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