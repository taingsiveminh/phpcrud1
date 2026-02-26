<?php
include 'db.php';
// Fetch all books from the database
$query = "SELECT * FROM books ORDER BY created_at DESC";
$result = $connection->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sale Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0 shadow-sm">
        <div class="px-3 py-3 lg:px-5">
            <div class="flex items-center justify-between max-w-6xl mx-auto">
                <div class="flex items-center space-x-8">
                    <span class="text-xl font-bold text-blue-600">
                        <i class="fas fa-book-open mr-2"></i>BookStore Admin
                    </span>
                    <div class="hidden md:flex space-x-4">
                        <a href="read.php" class="text-gray-500 hover:text-blue-600 font-medium transition-colors">Users</a>
                        <a href="books.php" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1">Books Inventory</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="add_book.php" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 transition-all shadow-md">
                        <i class="fas fa-plus mr-2"></i>Add New Book
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-24 px-4 pb-8 max-w-6xl mx-auto">
        <!-- Status Message -->
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 flex items-center shadow-sm">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span class="font-medium">Book added to inventory successfully!</span>
            </div>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Books List</h2>
                <span class="text-sm text-gray-500"><?= $result->num_rows ?> Books Total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Book Details</th>
                            <th class="px-6 py-4 font-semibold">Author</th>
                            <th class="px-6 py-4 font-semibold text-center">Price</th>
                            <th class="px-6 py-4 font-semibold text-center">Stock</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($book = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-8 bg-blue-100 text-blue-600 flex items-center justify-center rounded shadow-sm mr-3">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <span class="text-gray-900 font-semibold"><?= htmlspecialchars($book['title']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-600">
                                        <?= htmlspecialchars($book['author']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-green-600">
                                        $<?= number_format($book['price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $book['stock'] > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= $book['stock'] ?> left
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-1">
                                        <button class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-all inline-block">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-all inline-block">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-box-open text-5xl mb-4 opacity-20"></i>
                                    <p class="text-lg font-medium">No books in inventory yet.</p>
                                    <a href="add_book.php" class="text-blue-600 hover:underline">Add your first book</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>