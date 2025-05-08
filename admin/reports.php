<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is admin
if (!isAdmin()) {
    header('Location: /auth/login.php');
    exit;
}

// Get date range from query parameters
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Get overall statistics
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM Users WHERE role = 'student') as total_students,
        (SELECT COUNT(*) FROM Chapters) as total_chapters,
        (SELECT COUNT(*) FROM SubChapters) as total_subchapters,
        (SELECT COUNT(*) FROM UserQuizResults) as total_quiz_attempts
");
$stats = $stmt->fetch();

// Get student progress distribution
$stmt = $pdo->query("
    SELECT 
        CASE 
            WHEN level >= 10 THEN 'Advanced'
            WHEN level >= 5 THEN 'Intermediate'
            ELSE 'Beginner'
        END as level_group,
        COUNT(*) as count
    FROM Users 
    WHERE role = 'student'
    GROUP BY level_group
");
$levelDistribution = $stmt->fetchAll();

// Get quiz performance over time
$stmt = $pdo->prepare("
    SELECT 
        DATE(created_at) as date,
        AVG(score) as avg_score,
        COUNT(*) as attempts
    FROM UserQuizResults
    WHERE created_at BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date
");
$stmt->execute([$startDate, $endDate]);
$quizPerformance = $stmt->fetchAll();

// Get most active students
$stmt = $pdo->prepare("
    SELECT 
        u.name,
        u.level,
        u.points,
        COUNT(DISTINCT uqr.quiz_id) as quizzes_completed,
        COUNT(DISTINCT c.comment_id) as comments_made
    FROM Users u
    LEFT JOIN UserQuizResults uqr ON u.user_id = uqr.user_id
    LEFT JOIN Comments c ON u.user_id = c.user_id
    WHERE u.role = 'student'
    AND (uqr.created_at BETWEEN ? AND ? OR c.created_at BETWEEN ? AND ?)
    GROUP BY u.user_id
    ORDER BY u.points DESC
    LIMIT 10
");
$stmt->execute([$startDate, $endDate, $startDate, $endDate]);
$activeStudents = $stmt->fetchAll();

// Get chapter completion rates
$stmt = $pdo->query("
    SELECT 
        c.title,
        COUNT(DISTINCT uqr.user_id) as completed_count,
        (SELECT COUNT(*) FROM Users WHERE role = 'student') as total_students
    FROM Chapters c
    LEFT JOIN SubChapters s ON c.chapter_id = s.chapter_id
    LEFT JOIN Materials m ON s.subchapter_id = m.subchapter_id
    LEFT JOIN UserQuizResults uqr ON m.material_id = uqr.quiz_id
    GROUP BY c.chapter_id
    ORDER BY c.order_number
");
$chapterCompletion = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="manage_users.php" class="text-gray-300 hover:text-white">Manajemen User</a>
                </li>
                <li class="mb-4">
                    <a href="reports.php" class="text-white bg-gray-700 px-2 py-1 rounded">Laporan</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Laporan Analitik</h1>
                
                <!-- Date Range Filter -->
                <form class="mt-4 flex items-center space-x-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Dari</label>
                        <input type="date" id="start_date" name="start_date" 
                               value="<?= $startDate ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai</label>
                        <input type="date" id="end_date" name="end_date" 
                               value="<?= $endDate ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <button type="submit" class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Terapkan Filter
                    </button>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100">
                            <i class="fas fa-users text-indigo-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Siswa</p>
                            <p class="text-2xl font-semibold"><?= $stats['total_students'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-book text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Bab</p>
                            <p class="text-2xl font-semibold"><?= $stats['total_chapters'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <i class="fas fa-tasks text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Sub Bab</p>
                            <p class="text-2xl font-semibold"><?= $stats['total_subchapters'] ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <i class="fas fa-chart-bar text-purple-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Quiz Selesai</p>
                            <p class="text-2xl font-semibold"><?= $stats['total_quiz_attempts'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Quiz Performance Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Performa Quiz</h2>
                    <canvas id="quizChart"></canvas>
                </div>

                <!-- Student Level Distribution Chart -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Distribusi Level Siswa</h2>
                    <canvas id="levelChart"></canvas>
                </div>
            </div>

            <!-- Most Active Students -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Siswa Paling Aktif</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Level
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Poin
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quiz Selesai
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Komentar
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($activeStudents as $student): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($student['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= $student['level'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= $student['points'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= $student['quizzes_completed'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= $student['comments_made'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Chapter Completion Rates -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Tingkat Penyelesaian Bab</h2>
                    <div class="space-y-4">
                        <?php foreach ($chapterCompletion as $chapter): ?>
                            <?php 
                            $percentage = $chapter['total_students'] > 0 ? 
                                ($chapter['completed_count'] / $chapter['total_students']) * 100 : 0;
                            ?>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">
                                        <?= htmlspecialchars($chapter['title']) ?>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">
                                        <?= number_format($percentage, 1) ?>%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Quiz Performance Chart
    const quizData = <?= json_encode($quizPerformance) ?>;
    const quizCtx = document.getElementById('quizChart').getContext('2d');
    new Chart(quizCtx, {
        type: 'line',
        data: {
            labels: quizData.map(item => item.date),
            datasets: [{
                label: 'Rata-rata Nilai Quiz',
                data: quizData.map(item => item.avg_score),
                borderColor: 'rgb(79, 70, 229)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Student Level Distribution Chart
    const levelData = <?= json_encode($levelDistribution) ?>;
    const levelCtx = document.getElementById('levelChart').getContext('2d');
    new Chart(levelCtx, {
        type: 'doughnut',
        data: {
            labels: levelData.map(item => item.level_group),
            datasets: [{
                data: levelData.map(item => item.count),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    </script>
</body>
</html>
