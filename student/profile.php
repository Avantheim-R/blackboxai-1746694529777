<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is a student
if (!isStudent()) {
    header('Location: /auth/login.php');
    exit;
}

$error = '';
$success = '';

// Get user data
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM UserQuizResults WHERE user_id = u.user_id) as quizzes_taken,
           (SELECT COUNT(*) FROM Comments WHERE user_id = u.user_id) as total_comments
    FROM Users u 
    WHERE u.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        try {
            if (!empty($current_password)) {
                // Verify current password
                if (!password_verify($current_password, $user['password'])) {
                    throw new Exception('Password saat ini tidak valid');
                }

                // Validate new password
                if (empty($new_password)) {
                    throw new Exception('Password baru tidak boleh kosong');
                }
                if ($new_password !== $confirm_password) {
                    throw new Exception('Password baru tidak cocok');
                }
                if (strlen($new_password) < 6) {
                    throw new Exception('Password minimal 6 karakter');
                }

                // Update password
                $stmt = $pdo->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
                $stmt->execute([password_hash($new_password, PASSWORD_DEFAULT), $_SESSION['user_id']]);
            }

            // Update name
            if (!empty($name) && $name !== $user['name']) {
                $stmt = $pdo->prepare("UPDATE Users SET name = ? WHERE user_id = ?");
                $stmt->execute([$name, $_SESSION['user_id']]);
                $_SESSION['user_name'] = $name;
            }

            $success = 'Profil berhasil diperbarui';
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get recent activities
$stmt = $pdo->prepare("
    SELECT 
        'quiz' as type,
        m.title,
        uqr.score,
        uqr.created_at
    FROM UserQuizResults uqr
    JOIN Materials m ON uqr.quiz_id = m.material_id
    WHERE uqr.user_id = ?
    UNION ALL
    SELECT 
        'comment' as type,
        m.title,
        NULL as score,
        c.created_at
    FROM Comments c
    JOIN Materials m ON c.material_id = m.material_id
    WHERE c.user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$activities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="dashboard.php" class="text-xl font-bold text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <!-- Profile Overview -->
            <div class="md:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-center">
                        <div class="h-24 w-24 rounded-full bg-indigo-100 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user-circle text-4xl text-indigo-600"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-gray-500"><?= htmlspecialchars($user['nis']) ?></p>
                    </div>

                    <div class="mt-6">
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Level</span>
                            <span class="font-semibold"><?= $user['level'] ?></span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Poin</span>
                            <span class="font-semibold"><?= $user['points'] ?></span>
                        </div>
                        <div class="flex justify-between py-3 border-b">
                            <span class="text-gray-600">Quiz Selesai</span>
                            <span class="font-semibold"><?= $user['quizzes_taken'] ?></span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-600">Total Komentar</span>
                            <span class="font-semibold"><?= $user['total_comments'] ?></span>
                        </div>
                    </div>

                    <!-- Badges -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Badge</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $badges = json_decode($user['badges'] ?? '[]', true);
                            if (!empty($badges)):
                                foreach ($badges as $badge):
                            ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-medal mr-2"></i>
                                    <?= htmlspecialchars($badge) ?>
                                </span>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <p class="text-gray-500 text-sm">Belum ada badge yang diperoleh</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Settings -->
            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-6">Pengaturan Profil</h2>

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

                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['name']) ?>"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="mb-4">
                                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                                <input type="text" id="nis" 
                                       value="<?= htmlspecialchars($user['nis']) ?>"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm" 
                                       disabled>
                            </div>

                            <div class="border-t border-gray-200 mt-6 pt-6">
                                <h3 class="text-lg font-medium mb-4">Ubah Password</h3>
                                
                                <div class="mb-4">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                                        Password Saat Ini
                                    </label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div class="mb-4">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                                        Password Baru
                                    </label>
                                    <input type="password" id="new_password" name="new_password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                                        Konfirmasi Password Baru
                                    </label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Aktivitas Terbaru</h2>
                
                <div class="space-y-4">
                    <?php foreach ($activities as $activity): ?>
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center">
                                <?php if ($activity['type'] === 'quiz'): ?>
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-tasks text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            Menyelesaikan Quiz: <?= htmlspecialchars($activity['title']) ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Skor: <?= $activity['score'] ?>%
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-comment text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            Mengomentari: <?= htmlspecialchars($activity['title']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Password validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const currentPassword = document.getElementById('current_password').value;

        if (currentPassword && (!newPassword || !confirmPassword)) {
            e.preventDefault();
            alert('Silakan isi password baru dan konfirmasi password');
        } else if (newPassword && newPassword.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter');
        } else if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Password baru tidak cocok');
        }
    });
    </script>
</body>
</html>
