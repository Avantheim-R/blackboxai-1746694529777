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
    case 'POST':
        // Create new subchapter
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['chapter_id']) || !isset($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapter ID and title are required']);
            exit;
        }

        try {
            // Get max order number for this chapter
            $stmt = $pdo->prepare("SELECT MAX(order_number) as max_order FROM SubChapters WHERE chapter_id = ?");
            $stmt->execute([$data['chapter_id']]);
            $result = $stmt->fetch();
            $newOrder = ($result['max_order'] ?? 0) + 1;

            // Insert new subchapter
            $stmt = $pdo->prepare("INSERT INTO SubChapters (chapter_id, title, description, order_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['chapter_id'],
                $data['title'],
                $data['description'] ?? '',
                $newOrder
            ]);

            $subchapterId = $pdo->lastInsertId();

            // Create empty material for this subchapter
            $stmt = $pdo->prepare("INSERT INTO Materials (subchapter_id, title, content, type, phase) VALUES (?, ?, ?, 'text', 1)");
            $stmt->execute([
                $subchapterId,
                $data['title'],
                ''  // Empty content initially
            ]);

            echo json_encode([
                'success' => true,
                'subchapter_id' => $subchapterId,
                'message' => 'Subchapter created successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'PUT':
        // Update existing subchapter
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['subchapter_id']) || !isset($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Subchapter ID and title are required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE SubChapters SET title = ?, description = ? WHERE subchapter_id = ?");
            $stmt->execute([
                $data['title'],
                $data['description'] ?? '',
                $data['subchapter_id']
            ]);

            // Update associated material title
            $stmt = $pdo->prepare("UPDATE Materials SET title = ? WHERE subchapter_id = ?");
            $stmt->execute([
                $data['title'],
                $data['subchapter_id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Subchapter updated successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'DELETE':
        // Delete subchapter
        $subchapter_id = $_GET['subchapter_id'] ?? null;
        
        if (!$subchapter_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Subchapter ID is required']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Get chapter_id and current order for reordering
            $stmt = $pdo->prepare("SELECT chapter_id, order_number FROM SubChapters WHERE subchapter_id = ?");
            $stmt->execute([$subchapter_id]);
            $subchapter = $stmt->fetch();

            if ($subchapter) {
                // Delete associated material first
                $stmt = $pdo->prepare("DELETE FROM Materials WHERE subchapter_id = ?");
                $stmt->execute([$subchapter_id]);

                // Delete subchapter
                $stmt = $pdo->prepare("DELETE FROM SubChapters WHERE subchapter_id = ?");
                $stmt->execute([$subchapter_id]);

                // Reorder remaining subchapters
                $stmt = $pdo->prepare("UPDATE SubChapters SET order_number = order_number - 1 
                                     WHERE chapter_id = ? AND order_number > ?");
                $stmt->execute([
                    $subchapter['chapter_id'],
                    $subchapter['order_number']
                ]);
            }

            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Subchapter deleted successfully'
            ]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'PATCH':
        // Update subchapter order
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['subchapter_id']) || !isset($data['new_order'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Subchapter ID and new order are required']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Get current order and chapter_id
            $stmt = $pdo->prepare("SELECT chapter_id, order_number FROM SubChapters WHERE subchapter_id = ?");
            $stmt->execute([$data['subchapter_id']]);
            $current = $stmt->fetch();

            if ($current) {
                $currentOrder = $current['order_number'];
                $newOrder = $data['new_order'];
                $chapterId = $current['chapter_id'];

                if ($newOrder > $currentOrder) {
                    // Moving down - shift others up
                    $stmt = $pdo->prepare("UPDATE SubChapters SET order_number = order_number - 1 
                                         WHERE chapter_id = ? AND order_number > ? AND order_number <= ?");
                    $stmt->execute([$chapterId, $currentOrder, $newOrder]);
                } else {
                    // Moving up - shift others down
                    $stmt = $pdo->prepare("UPDATE SubChapters SET order_number = order_number + 1 
                                         WHERE chapter_id = ? AND order_number >= ? AND order_number < ?");
                    $stmt->execute([$chapterId, $newOrder, $currentOrder]);
                }

                // Set new position
                $stmt = $pdo->prepare("UPDATE SubChapters SET order_number = ? WHERE subchapter_id = ?");
                $stmt->execute([$newOrder, $data['subchapter_id']]);
            }

            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Subchapter order updated successfully'
            ]);
        } catch (PDOException $e) {
            $pdo->rollBack();
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
