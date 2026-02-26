<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$books = getBooksWithInventory($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">BookStore</a>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="books.php">Books</a></li>
                <li><a href="inventory.php" class="active">Inventory</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="new-sale.php">New Sale</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Inventory Management</h1>
                <a href="add-book.php" class="btn btn-primary">Add New Book</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Price</th>
                            <th>Stock Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): 
                            $status = '';
                            $statusClass = '';
                            if ($book['stock_quantity'] <= 0) {
                                $status = 'Out of Stock';
                                $statusClass = 'danger';
                            } elseif ($book['stock_quantity'] < 10) {
                                $status = 'Low Stock';
                                $statusClass = 'warning';
                            } else {
                                $status = 'In Stock';
                                $statusClass = 'success';
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td>$<?php echo number_format($book['price'], 2); ?></td>
                                <td><?php echo $book['stock_quantity']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $statusClass; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-warning btn-sm">Update Stock</a>
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

    <style>
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>

    <script src="assets/js/script.js"></script>
</body>
</html>