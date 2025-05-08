<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is admin
if (!isAdmin()) {
    header('Location: /auth/login.php');
    exit;
}

$error = '';
$success = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    $nis = $_POST['nis'] ?? '';
                    $name = $_POST['name'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $role = $_POST['role'] ?? 'student';

                    if (empty($nis) || empty($name) || empty($password)) {
                        throw new Exception('Semua field harus diisi');
                    }

                    // Check if NIS already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE nis = ?");
                    $stmt->execute([$nis]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception('NIS sudah terdaftar');
                    }

                    $stmt = $pdo->prepare("
                        INSERT INTO Users (nis, name, password, role) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $nis,
                        $name,
                        password_hash($password, PASSWORD_DEFAULT),
                        $role
                    ]);
                    $success = 'User berhasil ditambahkan';
                    break;

                case 'update':
                    $userId = $_POST['user_id'] ?? '';
                    $name = $_POST['name'] ?? '';
                    $role = $_POST['role'] ?? '';
                    $newPassword = $_POST['new_password'] ?? '';

                    if (empty($userId) || empty($name) || empty($role)) {
                        throw new Exception('Data tidak lengkap');
                    }

                    if (!empty($newPassword)) {
                        $stmt = $pdo->prepare("
                            UPDATE Users 
                            SET name = ?, role = ?, password = ? 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([
                            $name,
                            $role,
                            password_hash($newPassword, PASSWORD_DEFAULT),
                            $userId
                        ]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE Users 
                            SET name = ?, role = ? 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$name, $role, $userId]);
                    }
                    $success = 'User berhasil diperbarui';
                    break;

                case 'delete':
                    $userId = $_POST['user_id'] ?? '';
                    if (empty($userId)) {
                        throw new Exception('User ID tidak valid');
                    }

                    $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $success = 'User berhasil dihapus';
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get users list with pagination
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->query("SELECT COUNT(*) FROM Users");
$totalUsers = $stmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

$stmt = $pdo->prepare("
    SELECT u.*, 
           COUNT(DISTINCT uqr.quiz_id) as quizzes_taken,
           COUNT(DISTINCT c.comment_id) as total_comments
    FROM Users u
    LEFT JOIN UserQuizResults uqr ON u.user_id = uqr.user_id
    LEFT JOIN Comments c ON u.user_id = c.user_id
    GROUP BY u.user_id
    ORDER BY u.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$limit, $offset]);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <nav class="bg-gray-800 w-64 px-4 py-6">
            <div class="text-white text-xl font-semibold mb-8">Admin Panel</div>
            <ul>
                <li class="mb-4">
                    <a href="dashboard.php" class="text-gray-300 hover:text-white">Dashboard</a>
                </li>
                <li class="mb-4">
                    <a href="manage_chapters.php" class="text-gray-300 hover:text-white">Manajemen Bab</a>
                </li>
                <li class="mb-4">
                    <a href="manage_users.php" class="text-white bg-gray-700 px-2 py-1 rounded">Manajemen User</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="mb-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
                <button onclick="openAddUserModal()" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-user-plus mr-2"></i> Tambah User
                </button>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Progress
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($user['name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($user['nis']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        Level <?= $user['level'] ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= $user['quizzes_taken'] ?> quiz, <?= $user['total_comments'] ?> komentar
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= strtotime($user['last_login']) > strtotime('-7 days') ? 
                                                    'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                        <?= strtotime($user['last_login']) > strtotime('-7 days') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteUser(<?= $user['user_id'] ?>)"
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium"><?= $offset + 1 ?></span>
                                    to
                                    <span class="font-medium"><?= min($offset + $limit, $totalUsers) ?></span>
                                    of
                                    <span class="font-medium"><?= $totalUsers ?></span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?= $i ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?= $i === $page ? 'bg-gray-100' : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium" id="modalTitle">Tambah User Baru</h3>
                <form id="userForm" class="mt-4">
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" id="action" name="action" value="create">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nis">
                            NIS
                        </label>
                        <input type="text" id="nis" name="nis" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                            Role
                        </label>
                        <select id="role" name="role" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password
                        </label>
                        <input type="password" id="password" name="password"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openAddUserModal() {
        document.getElementById('modalTitle').textContent = 'Tambah User Baru';
        document.getElementById('userForm').reset();
        document.getElementById('action').value = 'create';
        document.getElementById('userId').value = '';
        document.getElementById('nis').disabled = false;
        document.getElementById('password').required = true;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(user) {
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = user.user_id;
        document.getElementById('nis').value = user.nis;
        document.getElementById('name').value = user.name;
        document.getElementById('role').value = user.role;
        document.getElementById('action').value = 'update';
        document.getElementById('nis').disabled = true;
        document.getElementById('password').required = false;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function deleteUser(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="${userId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
</body>
</html>
