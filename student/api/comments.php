<?php
session_start();
require_once('../../config/db.php');
require_once('../../includes/auth_check.php');

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Add new comment
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['material_id']) || !isset($data['comment']) || empty(trim($data['comment']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Material ID and comment are required']);
            exit;
        }

        try {
            // Verify material exists
            $stmt = $pdo->prepare("SELECT material_id FROM Materials WHERE material_id = ?");
            $stmt->execute([$data['material_id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Material not found');
            }

            // Insert comment
            $stmt = $pdo->prepare("
                INSERT INTO Comments (user_id, material_id, comment_text) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $data['material_id'],
                trim($data['comment'])
            ]);

            // Award points for participation
            $stmt = $pdo->prepare("
                UPDATE Users 
                SET points = points + 2 
                WHERE user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);

            // Get the newly created comment with user info
            $stmt = $pdo->prepare("
                SELECT c.*, u.name as user_name 
                FROM Comments c
                JOIN Users u ON c.user_id = u.user_id
                WHERE c.comment_id = ?
            ");
            $stmt->execute([$pdo->lastInsertId()]);
            $comment = $stmt->fetch();

            echo json_encode([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'comment_id' => $comment['comment_id'],
                    'user_name' => $comment['user_name'],
                    'comment_text' => $comment['comment_text'],
                    'created_at' => $comment['created_at']
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'GET':
        // Get comments for a material
        $material_id = $_GET['material_id'] ?? null;
        
        if (!$material_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Material ID is required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("
                SELECT c.*, u.name as user_name 
                FROM Comments c
                JOIN Users u ON c.user_id = u.user_id
                WHERE c.material_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$material_id]);
            $comments = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => array_map(function($comment) {
                    return [
                        'comment_id' => $comment['comment_id'],
                        'user_name' => $comment['user_name'],
                        'comment_text' => $comment['comment_text'],
                        'created_at' => $comment['created_at'],
                        'is_owner' => $comment['user_id'] == $_SESSION['user_id']
                    ];
                }, $comments)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'DELETE':
        // Delete a comment (only owner or admin can delete)
        $comment_id = $_GET['comment_id'] ?? null;
        
        if (!$comment_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment ID is required']);
            exit;
        }

        try {
            // Check if user owns the comment or is admin
            $stmt = $pdo->prepare("
                SELECT user_id 
                FROM Comments 
                WHERE comment_id = ?
            ");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch();

            if (!$comment || ($comment['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
                http_response_code(403);
                echo json_encode(['error' => 'Unauthorized to delete this comment']);
                exit;
            }

            // Delete the comment
            $stmt = $pdo->prepare("DELETE FROM Comments WHERE comment_id = ?");
            $stmt->execute([$comment_id]);

            echo json_encode([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
