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

$answers = $_POST['answers'] ?? [];

if (empty($answers)) {
    http_response_code(400);
    echo json_encode(['error' => 'No answers submitted']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Create exam record
    $stmt = $pdo->prepare("
        INSERT INTO FinalExam (user_id, created_at)
        VALUES (?, NOW())
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $examId = $pdo->lastInsertId();

    // Get all submitted question IDs
    $questionIds = array_keys($answers);

    // Get correct answers for these questions
    $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT question_id, correct_answer, question_type 
        FROM QuizQuestions 
        WHERE question_id IN ($placeholders)
    ");
    $stmt->execute($questionIds);
    $correctAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQuestions = count($correctAnswers);
    $correctCount = 0;
    $detailedResults = [];

    // Evaluate each answer
    foreach ($correctAnswers as $question) {
        $questionId = $question['question_id'];
        $userAnswer = json_decode($answers[$questionId] ?? '[]', true);
        $correctAnswer = json_decode($question['correct_answer'], true);
        $isCorrect = false;

        switch ($question['question_type']) {
            case 'drag_drop':
                $isCorrect = $userAnswer === $correctAnswer;
                break;

            case 'matching':
                $isCorrect = true;
                foreach ($userAnswer as $index => $answer) {
                    if ($answer !== $correctAnswer[$index]) {
                        $isCorrect = false;
                        break;
                    }
                }
                break;

            case 'canvas_simulation':
                // For canvas, implement a basic validation
                // You might want to implement more sophisticated image comparison
                $isCorrect = !empty($userAnswer);
                break;
        }

        if ($isCorrect) {
            $correctCount++;
        }

        $detailedResults[] = [
            'question_id' => $questionId,
            'is_correct' => $isCorrect
        ];
    }

    // Calculate final score
    $score = ($correctCount / $totalQuestions) * 100;

    // Update exam record with score
    $stmt = $pdo->prepare("UPDATE FinalExam SET score = ? WHERE exam_id = ?");
    $stmt->execute([$score, $examId]);

    // Generate certificate if passed (score >= 70)
    if ($score >= 70) {
        // Get user details
        $stmt = $pdo->prepare("SELECT name FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        // Create certificate record
        $certificatePath = generateCertificate($user['name'], $score, $examId);
        
        $stmt = $pdo->prepare("
            INSERT INTO Certificates (exam_id, user_id, certificate_pdf, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $examId,
            $_SESSION['user_id'],
            $certificatePath
        ]);

        // Award achievement points
        $pointsEarned = 100; // Base points for passing
        if ($score >= 90) $pointsEarned += 50; // Bonus for excellence
        
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET points = points + ?,
                level = FLOOR((points + ?) / 100) + 1
            WHERE user_id = ?
        ");
        $stmt->execute([$pointsEarned, $pointsEarned, $_SESSION['user_id']]);

        // Award completion badge
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET badges = JSON_ARRAY_APPEND(
                COALESCE(badges, '[]'),
                '$',
                'Course Completed'
            )
            WHERE user_id = ? AND NOT JSON_CONTAINS(COALESCE(badges, '[]'), '\"Course Completed\"')
        ");
        $stmt->execute([$_SESSION['user_id']]);

        $message = "Selamat! Anda telah lulus ujian akhir. Sertifikat Anda sedang diproses.";
    } else {
        $message = "Maaf, Anda belum mencapai nilai minimum kelulusan (70%). Silakan coba lagi.";
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'exam_id' => $examId,
        'score' => $score,
        'message' => $message,
        'passed' => $score >= 70,
        'detailed_results' => $detailedResults
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log($e->getMessage());
}

function generateCertificate($name, $score, $examId) {
    // Require TCPDF library
    require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('E-Learning System');
    $pdf->SetAuthor('Sistem Pembelajaran Desain Grafis');
    $pdf->SetTitle('Certificate of Completion');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage('L', 'A4');

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Certificate content
    $html = <<<EOD
    <style>
        h1 { color: #000; font-size: 30px; text-align: center; }
        .content { text-align: center; line-height: 1.5; }
        .signature { text-align: center; margin-top: 50px; }
    </style>
    <h1>Certificate of Completion</h1>
    <div class="content">
        <p>This is to certify that</p>
        <h2>{$name}</h2>
        <p>has successfully completed the</p>
        <h3>Graphic Design Course</h3>
        <p>with a score of {$score}%</p>
        <p>Date: {date('F d, Y')}</p>
    </div>
    <div class="signature">
        <p>_________________________</p>
        <p>Course Director</p>
    </div>
    EOD;

    // Print content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Create certificates directory if it doesn't exist
    $certificateDir = '../../uploads/certificates';
    if (!file_exists($certificateDir)) {
        mkdir($certificateDir, 0777, true);
    }

    // Save certificate
    $filename = "certificate_{$examId}.pdf";
    $filepath = "{$certificateDir}/{$filename}";
    $pdf->Output($filepath, 'F');

    return $filename;
}
