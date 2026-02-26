<?php
include 'db.php';

// 1. Fetch all users from database
$user_query = "SELECT * FROM users ORDER BY created_at DESC";
$user_result = $connection->query($user_query);

// 2. Fetch all books from database
$book_query = "SELECT * FROM books ORDER BY id DESC";
$book_result = $connection->query($book_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard</title>
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
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <span class="self-center text-xl font-bold sm:text-2xl whitespace-nowrap text-blue-600">
                        <i class="fas fa-store mr-2"></i>AdminPanel
                    </span>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="create.php" class="text-blue-600 border border-blue-600 hover:bg-blue-50 font-medium rounded-lg text-sm px-4 py-2 transition-all">
                        <i class="fas fa-user-plus mr-2"></i>New User
                    </a>
                    <a href="add_book.php" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 transition-all shadow-md">
                        <i class="fas fa-book-medical mr-2"></i>Add Book
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-24 px-4 pb-8 max-w-6xl mx-auto space-y-12">
        <!-- Status Messages -->
        <?php if(isset($_GET['msg'])): ?>
            <div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 flex items-center shadow-sm animate-pulse">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span class="font-medium">
                    <?php 
                        if($_GET['msg'] == 'created') echo "Entry created successfully!";
                        if($_GET['msg'] == 'updated') echo "Information updated!";
                        if($_GET['msg'] == 'deleted') echo "Record has been removed.";
                        if($_GET['msg'] == 'added') echo "New book added to inventory!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- SECTION 1: BOOK INVENTORY -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-book text-blue-500 mr-2"></i>Book Inventory</h2>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php echo $book_result->num_rows; ?> Books</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4">Title & Author</th>
                                <th class="px-6 py-4 text-center">Price</th>
                                <th class="px-6 py-4 text-center">Stock</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($book_result && $book_result->num_rows > 0): ?>
                                <?php while($row = $book_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($row['title']); ?></div>
                                            <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['author']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium text-green-600">
                                            $<?php echo number_format($row['price'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 rounded-lg text-xs font-bold <?php echo $row['stock'] > 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600'; ?>">
                                                <?php echo $row['stock']; ?> in stock
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-1">
                                            <a href="update_book.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-all inline-block">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_book.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-all inline-block">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">No books found in database.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- SECTION 2: USER MANAGEMENT -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-users text-purple-500 mr-2"></i>User Accounts</h2>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php echo $user_result->num_rows; ?> Users</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4">User Info</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($user_result && $user_result->num_rows > 0): ?>
                                <?php while($row = $user_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-purple-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold mr-3 text-xs">
                                                    <?php echo strtoupper(substr($row['username'] ?? 'U', 0, 1)); ?>
                                                </div>
                                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($row['username']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-medium">
                                            <?php echo htmlspecialchars($row['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-1">
                                            <a href="update.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-all inline-block">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-all inline-block">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>