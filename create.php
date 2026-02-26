<?php
include 'db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $hash);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: read.php?msg=created');
            exit;
        } else {
            $error = "Database Error: " . $connection->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New User</h2>
        
        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input name="username" type="text" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input name="password" type="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="pt-4 flex items-center justify-between">
                <a href="read.php" class="text-sm text-gray-500 hover:underline">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all shadow-md">Create User</button>
            </div>
        </form>
    </div>
</body>
</html>