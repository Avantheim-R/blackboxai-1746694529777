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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$subchapter_id = $_POST['subchapter_id'] ?? null;
$answers = $_POST['answers'] ?? [];

if (!$subchapter_id || empty($answers)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get questions and correct answers
    $stmt = $pdo->prepare("
        SELECT q.* 
        FROM QuizQuestions q
        JOIN Materials m ON q.material_id = m.material_id
        WHERE m.subchapter_id = ?
    ");
    $stmt->execute([$subchapter_id]);
    $questions = $stmt->fetchAll();

    $totalQuestions = count($questions);
    $correctAnswers = 0;

    // Evaluate each answer
    foreach ($questions as $question) {
        $questionId = $question['question_id'];
        $userAnswer = json_decode($answers[$questionId] ?? '[]', true);
        $correctAnswer = json_decode($question['correct_answer'], true);

        switch ($question['question_type']) {
            case 'drag_drop':
                // Compare arrays
                $isCorrect = $userAnswer === $correctAnswer;
                break;

            case 'matching':
                // Compare matching pairs
                $isCorrect = true;
                foreach ($userAnswer as $index => $answer) {
                    if ($answer !== $correctAnswer[$index]) {
                        $isCorrect = false;
                        break;
                    }
                }
                break;

            case 'canvas_simulation':
                // For canvas, we'll use a simplified validation
                // You might want to implement more sophisticated image comparison
                $isCorrect = !empty($userAnswer);
                break;

            default:
                $isCorrect = false;
        }

        if ($isCorrect) {
            $correctAnswers++;
        }

        // Store the answer
        $stmt = $pdo->prepare("
            INSERT INTO UserQuizResults (
                user_id, 
                quiz_id, 
                answer,
                is_correct,
                created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $questionId,
            json_encode($userAnswer),
            $isCorrect ? 1 : 0
        ]);
    }

    // Calculate score
    $score = ($correctAnswers / $totalQuestions) * 100;

    // Award points based on score
    $pointsEarned = 0;
    if ($score >= 90) $pointsEarned = 50;
    elseif ($score >= 80) $pointsEarned = 40;
    elseif ($score >= 70) $pointsEarned = 30;
    elseif ($score >= 60) $pointsEarned = 20;
    else $pointsEarned = 10;

    // Update user points and progress
    $stmt = $pdo->prepare("
        UPDATE Users 
        SET points = points + ?,
            level = FLOOR((points + ?) / 100) + 1
        WHERE user_id = ?
    ");
    $stmt->execute([$pointsEarned, $pointsEarned, $_SESSION['user_id']]);

    // Get chapter info for progress update
    $stmt = $pdo->prepare("
        SELECT chapter_id 
        FROM SubChapters 
        WHERE subchapter_id = ?
    ");
    $stmt->execute([$subchapter_id]);
    $chapter = $stmt->fetch();

    // Update progress to mark quiz as completed
    $stmt = $pdo->prepare("SELECT progress FROM Users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $progress = json_decode($stmt->fetch()['progress'] ?? '{}', true);

    if (!isset($progress[$chapter['chapter_id']])) {
        $progress[$chapter['chapter_id']] = [];
    }
    $progress[$chapter['chapter_id']]["quiz_{$subchapter_id}"] = [
        'completed' => true,
        'score' => $score,
        'date' => date('Y-m-d H:i:s')
    ];

    $stmt = $pdo->prepare("UPDATE Users SET progress = ? WHERE user_id = ?");
    $stmt->execute([json_encode($progress), $_SESSION['user_id']]);

    // Check for achievements/badges
    if ($score == 100) {
        // Perfect Score Badge
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET badges = JSON_ARRAY_APPEND(
                COALESCE(badges, '[]'),
                '$',
                'Perfect Score'
            )
            WHERE user_id = ? AND NOT JSON_CONTAINS(COALESCE(badges, '[]'), '\"Perfect Score\"')
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'score' => $score,
        'points_earned' => $pointsEarned,
        'message' => "Quiz completed successfully! You scored {$score}% and earned {$pointsEarned} points!"
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log($e->getMessage());
}
