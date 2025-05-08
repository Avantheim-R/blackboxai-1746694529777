<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is a student
if (!isStudent()) {
    header('Location: /auth/login.php');
    exit;
}

// Get student's progress
$stmt = $pdo->prepare("
    SELECT progress, points, level, badges 
    FROM Users 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get available chapters
$stmt = $pdo->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM SubChapters WHERE chapter_id = c.chapter_id) as subchapter_count
    FROM Chapters c 
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
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">Desain Grafis</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">
                            <i class="fas fa-user-circle mr-2"></i>
                            <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </span>
                        <a href="/auth/logout.php" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Progress Overview -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Progress Pembelajaran</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Level & Points -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600">Level</p>
                                <p class="text-2xl font-bold text-blue-800"><?= $user['level'] ?></p>
                            </div>
                            <div class="text-blue-500">
                                <i class="fas fa-star text-3xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-blue-600">Poin</p>
                            <p class="text-xl font-semibold text-blue-800"><?= $user['points'] ?? 0 ?></p>
                        </div>
                    </div>

                    <!-- Badges -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800 mb-3">Badge</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $badges = json_decode($user['badges'] ?? '[]', true);
                            if (!empty($badges)):
                                foreach ($badges as $badge):
                            ?>
                                <div class="flex items-center bg-purple-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-medal text-purple-500 mr-2"></i>
                                    <span class="text-sm text-purple-700"><?= htmlspecialchars($badge) ?></span>
                                </div>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <p class="text-sm text-purple-600">Belum ada badge yang diperoleh</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Next Goal -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 mb-3">Target Berikutnya</h3>
                        <div class="space-y-2">
                            <?php
                            $progress = json_decode($user['progress'] ?? '{}', true);
                            $nextChapter = null;
                            $nextSubchapter = null;
                            
                            foreach ($chapters as $chapter) {
                                if (!isset($progress[$chapter['chapter_id']]) || 
                                    count(array_filter($progress[$chapter['chapter_id']])) < $chapter['subchapter_count']) {
                                    $nextChapter = $chapter;
                                    break;
                                }
                            }
                            
                            if ($nextChapter):
                            ?>
                                <div class="flex items-center text-green-700">
                                    <i class="fas fa-tasks mr-2"></i>
                                    <span>Selesaikan <?= htmlspecialchars($nextChapter['title']) ?></span>
                                </div>
                            <?php else: ?>
                                <p class="text-green-700">Semua materi telah selesai!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Materials -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Materi Pembelajaran</h2>
                
                <div class="space-y-6">
                    <?php foreach ($chapters as $chapter): ?>
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <?= htmlspecialchars($chapter['title']) ?>
                                </h3>
                                <?php
                                $chapterProgress = $progress[$chapter['chapter_id']] ?? [];
                                $completedInChapter = count(array_filter($chapterProgress));
                                $progressPercentage = $chapter['subchapter_count'] > 0 ? 
                                    ($completedInChapter / $chapter['subchapter_count']) * 100 : 0;
                                ?>
                                <div class="text-sm text-gray-600">
                                    <?= $completedInChapter ?>/<?= $chapter['subchapter_count'] ?> selesai
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                <div class="bg-blue-600 h-2.5 rounded-full" 
                                     style="width: <?= $progressPercentage ?>%"></div>
                            </div>

                            <!-- Subchapters -->
                            <?php
                            $subStmt = $pdo->prepare("
                                SELECT * FROM SubChapters 
                                WHERE chapter_id = ? 
                                ORDER BY order_number
                            ");
                            $subStmt->execute([$chapter['chapter_id']]);
                            $subchapters = $subStmt->fetchAll();
                            ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($subchapters as $sub): ?>
                                    <?php
                                    $isCompleted = isset($progress[$chapter['chapter_id']][$sub['subchapter_id']]) && 
                                                  $progress[$chapter['chapter_id']][$sub['subchapter_id']];
                                    $isLocked = !empty($previousSubNotCompleted);
                                    ?>
                                    <a href="<?= $isLocked ? '#' : 'learn.php?id=' . $sub['subchapter_id'] ?>"
                                       class="block p-4 border rounded-lg hover:bg-gray-50 <?= $isLocked ? 'opacity-50 cursor-not-allowed' : '' ?>">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">
                                                <?= htmlspecialchars($sub['title']) ?>
                                            </span>
                                            <?php if ($isCompleted): ?>
                                                <i class="fas fa-check-circle text-green-500"></i>
                                            <?php elseif ($isLocked): ?>
                                                <i class="fas fa-lock text-gray-400"></i>
                                            <?php else: ?>
                                                <i class="fas fa-arrow-right text-blue-500"></i>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <?php
                                    $previousSubNotCompleted = !$isCompleted;
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add any interactive features here
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Show tooltip on locked items
        const lockedItems = document.querySelectorAll('.cursor-not-allowed');
        lockedItems.forEach(item => {
            item.title = 'Selesaikan materi sebelumnya terlebih dahulu';
        });
    });
    </script>
</body>
</html>
