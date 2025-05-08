<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistem Pembelajaran Desain Grafis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <nav class="bg-gray-800 w-64 px-4 py-6">
        <div class="flex items-center mb-8">
            <span class="text-white text-2xl font-semibold">Admin Panel</span>
        </div>
        <ul class="space-y-4">
            <li>
                <a href="dashboard.php" class="flex items-center text-white bg-gray-700 px-4 py-2 rounded">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="manage_chapters.php" class="flex items-center text-gray-300 hover:text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-book mr-3"></i>
                    Manajemen Bab
                </a>
            </li>
            <li>
                <a href="manage_users.php" class="flex items-center text-gray-300 hover:text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-users mr-3"></i>
                    Manajemen User
                </a>
            </li>
            <li>
                <a href="reports.php" class="flex items-center text-gray-300 hover:text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Laporan
                </a>
            </li>
        </ul>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <a href="../auth/logout.php" class="flex items-center text-gray-300 hover:text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-sign-out-alt mr-3"></i>
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-gray-600">Selamat datang di panel admin sistem pembelajaran desain grafis.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            // Get total students
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Users WHERE role = 'student'");
            $students = $stmt->fetch()['count'];

            // Get total chapters
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Chapters");
            $chapters = $stmt->fetch()['count'];

            // Get total materials
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Materials");
            $materials = $stmt->fetch()['count'];

            // Get active users today
            $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as count FROM UserQuizResults WHERE DATE(created_at) = CURDATE()");
            $activeUsers = $stmt->fetch()['count'];
            ?>

            <!-- Total Students Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Siswa</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $students ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Chapters Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                        <i class="fas fa-book text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Bab</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $chapters ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Materials Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                        <i class="fas fa-file-alt text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Total Materi</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $materials ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                        <i class="fas fa-user-clock text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm">Siswa Aktif Hari Ini</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?= $activeUsers ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $stmt = $pdo->query("
                            SELECT 
                                u.name,
                                uqr.score,
                                uqr.created_at,
                                m.title as material_title
                            FROM UserQuizResults uqr
                            JOIN Users u ON uqr.user_id = u.user_id
                            JOIN Materials m ON uqr.quiz_id = m.material_id
                            ORDER BY uqr.created_at DESC
                            LIMIT 5
                        ");
                        while ($row = $stmt->fetch()) {
                            $status = $row['score'] >= 70 ? 
                                '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lulus</span>' : 
                                '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>';
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">Mengerjakan Quiz</div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($row['material_title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= $status ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
