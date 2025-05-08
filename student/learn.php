<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is a student
if (!isStudent()) {
    header('Location: /auth/login.php');
    exit;
}

$subchapter_id = $_GET['id'] ?? null;
if (!$subchapter_id) {
    header('Location: dashboard.php');
    exit;
}

// Get subchapter and material details
$stmt = $pdo->prepare("
    SELECT s.*, c.title as chapter_title, m.* 
    FROM SubChapters s
    JOIN Chapters c ON s.chapter_id = c.chapter_id
    LEFT JOIN Materials m ON s.subchapter_id = m.subchapter_id
    WHERE s.subchapter_id = ?
");
$stmt->execute([$subchapter_id]);
$material = $stmt->fetch();

if (!$material) {
    header('Location: dashboard.php');
    exit;
}

// Get user progress
$stmt = $pdo->prepare("SELECT progress FROM Users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userProgress = json_decode($stmt->fetch()['progress'] ?? '{}', true);

// Check if previous subchapter is completed
$stmt = $pdo->prepare("
    SELECT s.subchapter_id 
    FROM SubChapters s 
    WHERE s.chapter_id = ? AND s.order_number < ?
    ORDER BY s.order_number DESC 
    LIMIT 1
");
$stmt->execute([$material['chapter_id'], $material['order_number']]);
$prevSubchapter = $stmt->fetch();

if ($prevSubchapter) {
    $isPrevCompleted = isset($userProgress[$material['chapter_id']][$prevSubchapter['subchapter_id']]) && 
                      $userProgress[$material['chapter_id']][$prevSubchapter['subchapter_id']];
    if (!$isPrevCompleted) {
        header('Location: dashboard.php');
        exit;
    }
}

// Get comments for this material
$stmt = $pdo->prepare("
    SELECT c.*, u.name as user_name 
    FROM Comments c
    JOIN Users u ON c.user_id = u.user_id
    WHERE c.material_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$material['material_id']]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($material['title']) ?> - Sistem Pembelajaran Desain Grafis</title>
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
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700">
                        <i class="fas fa-user-circle mr-2"></i>
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-gray-600 mb-4" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="dashboard.php">Dashboard</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <?= htmlspecialchars($material['chapter_title']) ?>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center text-gray-800">
                    <?= htmlspecialchars($material['title']) ?>
                </li>
            </ol>
        </nav>

        <!-- Main Content -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">
                    <?= htmlspecialchars($material['title']) ?>
                </h1>

                <!-- Content based on material type -->
                <?php if ($material['type'] === 'text'): ?>
                    <div class="prose max-w-none">
                        <?= $material['content'] ?>
                    </div>
                <?php elseif ($material['type'] === 'video'): ?>
                    <div class="aspect-w-16 aspect-h-9 mb-4">
                        <iframe src="<?= htmlspecialchars($material['content']) ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                class="w-full h-full"></iframe>
                    </div>
                <?php elseif ($material['type'] === 'game'): ?>
                    <div id="game-container" class="w-full min-h-[400px] border rounded-lg">
                        <?= $material['content'] ?>
                    </div>
                <?php endif; ?>

                <!-- Mark as Complete Button -->
                <div class="mt-8 flex justify-end">
                    <button onclick="markAsComplete(<?= $material['subchapter_id'] ?>)"
                            class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <i class="fas fa-check mr-2"></i>
                        Tandai Selesai
                    </button>
                </div>

                <!-- Comments Section -->
                <div class="mt-8 border-t pt-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Diskusi</h2>
                    
                    <!-- Comment Form -->
                    <form id="commentForm" class="mb-6">
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700">Tulis Komentar</label>
                            <textarea id="comment" name="comment" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div id="commentsList" class="space-y-4">
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            <?= htmlspecialchars($comment['user_name']) ?>
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            <?= date('d M Y H:i', strtotime($comment['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 text-gray-700">
                                    <?= nl2br(htmlspecialchars($comment['comment_text'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Mark material as complete
    function markAsComplete(subchapterId) {
        fetch('api/progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subchapter_id: subchapterId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Materi berhasil ditandai selesai!');
                window.location.href = 'dashboard.php';
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }

    // Handle comment submission
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const comment = document.getElementById('comment').value;
        if (!comment.trim()) return;

        fetch('api/comments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                material_id: <?= $material['material_id'] ?>,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('comment').value = '';
                // Reload comments
                location.reload();
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });
    </script>
</body>
</html>
