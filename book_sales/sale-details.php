<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: sales.php');
    exit;
}

$sale_id = $_GET['id'];
$items = getSaleDetails($conn, $sale_id);

if (empty($items)) {
    header('Location: sales.php?msg=Sale not found&type=danger');
    exit;
}

// Get sale info
$result = mysqli_query($conn, "SELECT * FROM sales WHERE sale_id = $sale_id");
$sale = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Details - BookStore</title>
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
                <h1>Sale Details #<?php echo $sale_id; ?></h1>
                <a href="sales.php" class="btn btn-primary">Back to Sales</a>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <h3>Sale Information</h3>
                <p><strong>Date:</strong> <?php echo date('F d, Y H:i:s', strtotime($sale['sale_date'])); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($sale['total_amount'], 2); ?></p>
            </div>

            <div class="card">
                <h3>Items Sold</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align: right;">Total:</th>
                                <th>$<?php echo number_format($sale['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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