<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is a student
if (!isStudent()) {
    header('Location: /auth/login.php');
    exit;
}

// Get student progress
$stmt = $pdo->prepare("
    SELECT 
        u.*,
        (SELECT COUNT(*) FROM UserQuizResults WHERE user_id = u.user_id) as completed_quizzes,
        (SELECT COUNT(*) FROM Comments WHERE user_id = u.user_id) as total_comments
    FROM Users u 
    WHERE u.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

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

// Get available chapters
$stmt = $pdo->query("
    SELECT c.*, 
           COUNT(DISTINCT s.subchapter_id) as total_subchapters,
           COUNT(DISTINCT m.material_id) as total_materials
    FROM Chapters c
    LEFT JOIN SubChapters s ON c.chapter_id = s.chapter_id
    LEFT JOIN Materials m ON s.subchapter_id = m.subchapter_id
    GROUP BY c.chapter_id
    ORDER BY c.order_number
");
$chapters = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f4f6;
        }
        .gradient-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        .chapter-card {
            transition: all 0.3s ease;
        }
        .chapter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .activity-item {
            transition: all 0.3s ease;
        }
        .activity-item:hover {
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-indigo-600">DesignEdu</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="profile.php" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-user-circle mr-2"></i>
                        <?= htmlspecialchars($user['name']) ?>
                    </a>
                    <a href="/auth/logout.php" class="ml-4 text-red-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="gradient-card rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Level Saat Ini</p>
                        <h3 class="text-3xl font-bold"><?= $user['level'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <?php $progress = ($user['points'] % 100) ?>
                        <div class="bg-white h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                    </div>
                    <p class="text-sm mt-2"><?= $progress ?>% menuju level berikutnya</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Kuis Selesai</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $user['completed_quizzes'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600">
                        Total Poin: <span class="font-semibold text-gray-800"><?= $user['points'] ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Kontribusi</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $user['total_comments'] ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-comments text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-sm text-gray-600">
                        Komentar & Diskusi
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Chapters List -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Materi Pembelajaran</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($chapters as $chapter): ?>
                        <div class="chapter-card bg-white rounded-lg shadow-lg overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <?= htmlspecialchars($chapter['title']) ?>
                                        </h3>
                                        <p class="text-gray-600 mt-1">
                                            <?= $chapter['total_subchapters'] ?> Sub Bab • 
                                            <?= $chapter['total_materials'] ?> Materi
                                        </p>
                                    </div>
                                    <div class="text-indigo-600">
                                        <i class="fas fa-book-open text-2xl"></i>
                                    </div>
                                </div>
                                <p class="text-gray-600 mt-4">
                                    <?= htmlspecialchars($chapter['description']) ?>
                                </p>
                                <div class="mt-6">
                                    <a href="learn.php?chapter=<?= $chapter['chapter_id'] ?>" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Activities -->
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Aktivitas Terbaru</h2>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($activities as $activity): ?>
                            <div class="activity-item p-4">
                                <div class="flex items-center">
                                    <?php if ($activity['type'] === 'quiz'): ?>
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-tasks text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                Menyelesaikan Kuis
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                <?= htmlspecialchars($activity['title']) ?>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Skor: <?= $activity['score'] ?>%
                                            </p>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-comment text-green-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                Menambahkan Komentar
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Pada: <?= htmlspecialchars($activity['title']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="ml-auto text-sm text-gray-500">
                                        <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add smooth scroll animation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
