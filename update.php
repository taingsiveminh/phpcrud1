<?php
include 'db.php';

// 1. Get the ID from the URL safely
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: read.php");
    exit;
}

// 2. Fetch the existing user data from the database
$stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user doesn't exist, go back to dashboard
if (!$user) {
    header("Location: read.php");
    exit;
}

// 3. Handle the Form Submission (Update Logic)
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email)) {
        $error = "Username and Email are required.";
    } else {
        // If password is provided, update it too. Otherwise, keep the old one.
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $connection->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $updateStmt->bind_param("sssi", $username, $email, $hashedPassword, $id);
        } else {
            $updateStmt = $connection->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $updateStmt->bind_param("ssi", $username, $email, $id);
        }

        if ($updateStmt->execute()) {
            header("Location: read.php?msg=updated");
            exit;
        } else {
            $error = "Update failed: " . $connection->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - <?= htmlspecialchars($user['username']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="flex items-center mb-6">
            <a href="read.php" class="text-gray-400 hover:text-blue-600 mr-4 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" required 
                       value="<?= htmlspecialchars($user['username']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required 
                       value="<?= htmlspecialchars($user['email']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    New Password <span class="text-gray-400 font-normal">(Leave blank to keep current)</span>
                </label>
                <input type="password" name="password" 
                       placeholder="••••••••"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

</body>
</html>