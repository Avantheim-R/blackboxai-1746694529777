<?php
session_start();
require_once('../../config/db.php');
require_once('../../includes/auth_check.php');

header('Content-Type: application/json');

// Ensure user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get content for a subchapter
        $subchapter_id = $_GET['subchapter_id'] ?? null;
        
        if (!$subchapter_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Subchapter ID is required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("
                SELECT m.*, s.title as subchapter_title 
                FROM Materials m 
                JOIN SubChapters s ON m.subchapter_id = s.subchapter_id 
                WHERE m.subchapter_id = ?
            ");
            $stmt->execute([$subchapter_id]);
            $material = $stmt->fetch();

            if ($material) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'content' => $material['content'],
                        'title' => $material['subchapter_title'],
                        'type' => $material['type'],
                        'phase' => $material['phase']
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Content not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'PUT':
        // Update content for a subchapter
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['subchapter_id']) || !isset($data['content'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Subchapter ID and content are required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE Materials 
                SET content = ?, 
                    type = ?,
                    phase = ?
                WHERE subchapter_id = ?
            ");
            $stmt->execute([
                $data['content'],
                $data['type'] ?? 'text',
                $data['phase'] ?? 1,
                $data['subchapter_id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Content updated successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'POST':
        // Handle file uploads for the rich text editor
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];
        $fileName = uniqid() . '_' . basename($file['name']);
        $uploadDir = '../../uploads/content/';
        $uploadPath = $uploadDir . $fileName;

        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type']);
            exit;
        }

        try {
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                echo json_encode([
                    'success' => true,
                    'location' => '/uploads/content/' . $fileName
                ]);
            } else {
                throw new Exception('Failed to move uploaded file');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Upload failed']);
            error_log($e->getMessage());
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
