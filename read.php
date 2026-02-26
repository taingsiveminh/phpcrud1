<?php
include 'db.php';
$rows = [];
$res = $connection->query("SELECT id, username, email, created_at FROM users ORDER BY id DESC");
if ($res) {
    while ($r = $res->fetch_assoc()) $rows[] = $r;
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
        <div class="px-3 py-3 lg:px-5">
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold text-blue-600"><i class="fas fa-users mr-2"></i>User Admin</span>
                <a href="create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Add User
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-20 px-4 pb-8 max-w-6xl mx-auto">
        <?php if ($msg): ?>
            <div class="mb-4 p-4 rounded-lg <?= $msg === 'deleted' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200' ?>">
                <i class="fas fa-check-circle mr-2"></i> User successfully <?= htmlspecialchars($msg) ?>!
            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Created</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No users found in database.</td></tr>
                        <?php else: foreach ($rows as $r): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">#<?= $r['id'] ?></td>
                                <td class="px-6 py-4 font-medium text-gray-700"><?= htmlspecialchars($r['username']) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($r['email']) ?></td>
                                <td class="px-6 py-4 text-gray-500"><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="update.php?id=<?= $r['id'] ?>" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors inline-block" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this user?')" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors inline-block" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>