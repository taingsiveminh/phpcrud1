<?php
/**
 * ADD_BOOK.PHP
 * This file handles the form for adding new books to the 'books' table.
 * It includes a diagnostic section to help troubleshoot common XAMPP errors.
 */

// 1. DIAGNOSTICS: Check if the connection file exists
if (!file_exists('db.php')) {
    die("<div style='font-family:sans-serif; padding:20px; background:#fee2e2; color:#b91c1c; border-radius:10px; border:1px solid #f87171; margin:20px;'>
            <h3 style='margin-top:0;'>Missing Configuration File</h3>
            <p><strong>Error:</strong> 'db.php' was not found in your htdocs folder.</p>
            <p>Please create a file named <code>db.php</code> and paste your database connection code there.</p>
         </div>");
}

include 'db.php';

// 2. CONNECTION CHECK: Verify the database is reachable
if (isset($connection) && $connection->connect_error) {
    die("<div style='font-family:sans-serif; padding:20px; background:#fff7ed; color:#9a3412; border-radius:10px; border:1px solid #fbbf24; margin:20px;'>
            <h3 style='margin-top:0;'>Database Connection Failed</h3>
            <p><strong>Message:</strong> " . $connection->connect_error . "</p>
            <p><strong>Tip:</strong> Check your <code>db.php</code> file. Ensure the DB_PASSWORD is correct (usually empty '' in XAMPP) and DB_NAME is 'userdb'.</p>
         </div>");
}

$error = "";
$success = false;

// 3. FORM PROCESSING: Handle the POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    if (empty($title) || empty($author)) {
        $error = "Title and Author are required fields.";
    } else {
        // Prepare the statement to prevent SQL Injection
        $stmt = $connection->prepare("INSERT INTO books (title, author, price, stock) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssdi", $title, $author, $price, $stock);
            
            if ($stmt->execute()) {
                // Redirect to the list view (make sure book.php exists)
                header("Location: book.php?msg=added");
                exit;
            } else {
                $error = "Execution Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Preparation Error: " . $connection->error . " (Check if table 'books' exists in 'userdb')";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book - Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle text-blue-600 mr-3"></i>Add New Book
            </h2>
            <a href="book.php" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>

        <!-- Feedback Messages -->
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm flex items-start">
                <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
                <div>
                    <p class="font-bold">Entry Failed</p>
                    <p class="opacity-90"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Book Title</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-book"></i>
                    </span>
                    <input type="text" name="title" required placeholder="e.g. Atomic Habits" 
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-300">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Author Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user-edit"></i>
                    </span>
                    <input type="text" name="author" required placeholder="e.g. James Clear" 
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all placeholder:text-gray-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Price ($)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-tag"></i>
                        </span>
                        <input type="number" step="0.01" name="price" required placeholder="0.00" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Stock Qty</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-boxes"></i>
                        </span>
                        <input type="number" name="stock" required placeholder="0" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="book.php" class="text-sm font-semibold text-gray-400 hover:text-gray-600 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-100 transition-all hover:-translate-y-1 active:translate-y-0 flex items-center">
                    <i class="fas fa-save mr-2"></i> Save Book
                </button>
            </div>
        </form>
    </div>
</body>
</html>