<?php
session_start();
require_once('../../config/db.php');
require_once('../../includes/auth_check.php');

header('Content-Type: application/json');

// Ensure user is a student
if (!isStudent()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $subchapter_id = $data['subchapter_id'] ?? null;

    if (!$subchapter_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Subchapter ID is required']);
        exit;
    }

    try {
        // Get chapter ID for this subchapter
        $stmt = $pdo->prepare("SELECT chapter_id FROM SubChapters WHERE subchapter_id = ?");
        $stmt->execute([$subchapter_id]);
        $chapter = $stmt->fetch();

        if (!$chapter) {
            throw new Exception('Subchapter not found');
        }

        // Get current progress
        $stmt = $pdo->prepare("SELECT progress, points FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        $progress = json_decode($user['progress'] ?? '{}', true);
        $points = (int)$user['points'];

        // Update progress
        if (!isset($progress[$chapter['chapter_id']])) {
            $progress[$chapter['chapter_id']] = [];
        }
        $progress[$chapter['chapter_id']][$subchapter_id] = true;

        // Award points for completion
        $points += 10; // Base points for completing a subchapter

        // Check if chapter is complete
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM SubChapters 
            WHERE chapter_id = ?
        ");
        $stmt->execute([$chapter['chapter_id']]);
        $totalSubchapters = $stmt->fetch()['total'];

        $completedSubchapters = count($progress[$chapter['chapter_id']]);
        if ($completedSubchapters === (int)$totalSubchapters) {
            $points += 50; // Bonus points for completing a chapter
        }

        // Calculate level based on points
        $level = floor($points / 100) + 1;

        // Update user progress and points
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET progress = ?, 
                points = ?,
                level = ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            json_encode($progress),
            $points,
            $level,
            $_SESSION['user_id']
        ]);

        // Check and award badges
        $badges = [];
        if ($points >= 100) $badges[] = "Beginner";
        if ($points >= 500) $badges[] = "Intermediate";
        if ($points >= 1000) $badges[] = "Advanced";
        if ($points >= 2000) $badges[] = "Expert";

        if (!empty($badges)) {
            $stmt = $pdo->prepare("UPDATE Users SET badges = ? WHERE user_id = ?");
            $stmt->execute([json_encode($badges), $_SESSION['user_id']]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => [
                'points' => $points,
                'level' => $level,
                'badges' => $badges
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
        error_log($e->getMessage());
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
