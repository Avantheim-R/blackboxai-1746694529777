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
        // Create new chapter
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['title']) || empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }

        try {
            // Get max order number
            $stmt = $pdo->query("SELECT MAX(order_number) as max_order FROM Chapters");
            $result = $stmt->fetch();
            $newOrder = ($result['max_order'] ?? 0) + 1;

            $stmt = $pdo->prepare("INSERT INTO Chapters (title, description, order_number) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['title'],
                $data['description'] ?? '',
                $newOrder
            ]);

            echo json_encode([
                'success' => true,
                'chapter_id' => $pdo->lastInsertId(),
                'message' => 'Chapter created successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'PUT':
        // Update existing chapter
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['chapter_id']) || !isset($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapter ID and title are required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE Chapters SET title = ?, description = ? WHERE chapter_id = ?");
            $stmt->execute([
                $data['title'],
                $data['description'] ?? '',
                $data['chapter_id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Chapter updated successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'DELETE':
        // Delete chapter
        $chapter_id = $_GET['chapter_id'] ?? null;
        
        if (!$chapter_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapter ID is required']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM Chapters WHERE chapter_id = ?");
            $stmt->execute([$chapter_id]);

            // Reorder remaining chapters
            $stmt = $pdo->query("SET @count = 0");
            $stmt = $pdo->query("UPDATE Chapters SET order_number = @count:= @count + 1 ORDER BY order_number");

            echo json_encode([
                'success' => true,
                'message' => 'Chapter deleted successfully'
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
            error_log($e->getMessage());
        }
        break;

    case 'PATCH':
        // Update chapter order
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['chapter_id']) || !isset($data['new_order'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapter ID and new order are required']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Get current order
            $stmt = $pdo->prepare("SELECT order_number FROM Chapters WHERE chapter_id = ?");
            $stmt->execute([$data['chapter_id']]);
            $current = $stmt->fetch();

            if ($current) {
                $currentOrder = $current['order_number'];
                $newOrder = $data['new_order'];

                if ($newOrder > $currentOrder) {
                    // Moving down - shift others up
                    $stmt = $pdo->prepare("UPDATE Chapters SET order_number = order_number - 1 
                                         WHERE order_number > ? AND order_number <= ?");
                    $stmt->execute([$currentOrder, $newOrder]);
                } else {
                    // Moving up - shift others down
                    $stmt = $pdo->prepare("UPDATE Chapters SET order_number = order_number + 1 
                                         WHERE order_number >= ? AND order_number < ?");
                    $stmt->execute([$newOrder, $currentOrder]);
                }

                // Set new position
                $stmt = $pdo->prepare("UPDATE Chapters SET order_number = ? WHERE chapter_id = ?");
                $stmt->execute([$newOrder, $data['chapter_id']]);
            }

            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Chapter order updated successfully'
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
