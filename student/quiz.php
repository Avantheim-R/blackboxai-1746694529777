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

// Check if student has completed the material
$stmt = $pdo->prepare("
    SELECT progress 
    FROM Users 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$progress = json_decode($stmt->fetch()['progress'] ?? '{}', true);

// Get subchapter info
$stmt = $pdo->prepare("
    SELECT s.*, c.title as chapter_title 
    FROM SubChapters s
    JOIN Chapters c ON s.chapter_id = c.chapter_id
    WHERE s.subchapter_id = ?
");
$stmt->execute([$subchapter_id]);
$subchapter = $stmt->fetch();

if (!$subchapter) {
    header('Location: dashboard.php');
    exit;
}

// Check if previous material is completed
if (!isset($progress[$subchapter['chapter_id']][$subchapter_id])) {
    header('Location: learn.php?id=' . $subchapter_id);
    exit;
}

// Get quiz questions
$stmt = $pdo->prepare("
    SELECT * FROM QuizQuestions 
    WHERE material_id = (
        SELECT material_id 
        FROM Materials 
        WHERE subchapter_id = ?
    )
    ORDER BY RAND()
    LIMIT 5
");
$stmt->execute([$subchapter_id]);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis - <?= htmlspecialchars($subchapter['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
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
                    <div id="timer" class="text-lg font-semibold text-gray-700">
                        <i class="fas fa-clock mr-2"></i>
                        <span>00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Kuis: <?= htmlspecialchars($subchapter['title']) ?>
            </h1>

            <form id="quizForm">
                <input type="hidden" name="subchapter_id" value="<?= $subchapter_id ?>">
                
                <div id="questions" class="space-y-8">
                    <?php foreach ($questions as $index => $question): ?>
                        <?php
                        $options = json_decode($question['options'], true);
                        $questionType = $question['question_type'];
                        ?>
                        <div class="question-container border rounded-lg p-6" data-question-id="<?= $question['question_id'] ?>">
                            <h3 class="text-lg font-semibold mb-4">
                                <?= ($index + 1) . '. ' . htmlspecialchars($question['question_text']) ?>
                            </h3>

                            <?php if ($questionType === 'drag_drop'): ?>
                                <div class="drag-drop-container">
                                    <div class="options-container grid grid-cols-2 gap-4 mb-4">
                                        <div class="draggable-items space-y-2">
                                            <?php foreach ($options as $option): ?>
                                                <div class="draggable bg-blue-100 p-2 rounded cursor-move">
                                                    <?= htmlspecialchars($option) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="droppable-zones space-y-2">
                                            <?php foreach ($options as $index => $option): ?>
                                                <div class="droppable bg-gray-100 p-2 rounded min-h-[40px]" 
                                                     data-position="<?= $index ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                            <?php elseif ($questionType === 'matching'): ?>
                                <div class="matching-container grid grid-cols-2 gap-4">
                                    <div class="left-items space-y-2">
                                        <?php foreach ($options['left'] as $index => $item): ?>
                                            <div class="bg-blue-100 p-2 rounded">
                                                <?= htmlspecialchars($item) ?>
                                                <select class="matching-select ml-2" 
                                                        name="matching[<?= $question['question_id'] ?>][<?= $index ?>]">
                                                    <option value="">Pilih jawaban...</option>
                                                    <?php foreach ($options['right'] as $rightIndex => $rightItem): ?>
                                                        <option value="<?= $rightIndex ?>">
                                                            <?= htmlspecialchars($rightItem) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            <?php elseif ($questionType === 'canvas_simulation'): ?>
                                <div class="canvas-container">
                                    <canvas id="canvas-<?= $question['question_id'] ?>" 
                                            class="border rounded"
                                            width="600" height="400"></canvas>
                                    <div class="tools mt-2 space-x-2">
                                        <button type="button" class="tool-btn" data-tool="brush">
                                            <i class="fas fa-paint-brush"></i>
                                        </button>
                                        <button type="button" class="tool-btn" data-tool="eraser">
                                            <i class="fas fa-eraser"></i>
                                        </button>
                                        <input type="color" class="color-picker">
                                        <input type="range" class="brush-size" min="1" max="20" value="5">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Timer functionality
    let timeLeft = 600; // 10 minutes
    const timerDisplay = document.querySelector('#timer span');
    
    const timer = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft === 0) {
            clearInterval(timer);
            document.getElementById('quizForm').submit();
        }
        timeLeft--;
    }, 1000);

    // Drag and drop functionality
    $('.draggable').draggable({
        revert: 'invalid',
        helper: 'clone'
    });

    $('.droppable').droppable({
        accept: '.draggable',
        drop: function(event, ui) {
            $(this).empty().append(ui.helper.clone().removeClass('ui-draggable ui-draggable-handle'));
            ui.helper.remove();
        }
    });

    // Canvas functionality
    document.querySelectorAll('canvas').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let tool = 'brush';
        
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        function startDrawing(e) {
            isDrawing = true;
            draw(e);
        }

        function draw(e) {
            if (!isDrawing) return;
            
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ctx.lineWidth = document.querySelector('.brush-size').value;
            ctx.lineCap = 'round';
            
            if (tool === 'brush') {
                ctx.strokeStyle = document.querySelector('.color-picker').value;
            } else {
                ctx.strokeStyle = '#ffffff';
            }
            
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.beginPath();
        }
    });

    // Tool selection
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            tool = btn.dataset.tool;
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // Form submission
    document.getElementById('quizForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('subchapter_id', this.querySelector('[name="subchapter_id"]').value);

        // Collect answers
        document.querySelectorAll('.question-container').forEach(container => {
            const questionId = container.dataset.questionId;
            
            if (container.querySelector('.drag-drop-container')) {
                const answers = [];
                container.querySelectorAll('.droppable').forEach(zone => {
                    const item = zone.querySelector('.draggable');
                    answers.push(item ? item.textContent.trim() : '');
                });
                formData.append(`answers[${questionId}]`, JSON.stringify(answers));
            }
            
            else if (container.querySelector('.matching-container')) {
                const answers = {};
                container.querySelectorAll('.matching-select').forEach((select, index) => {
                    answers[index] = select.value;
                });
                formData.append(`answers[${questionId}]`, JSON.stringify(answers));
            }
            
            else if (container.querySelector('canvas')) {
                const canvas = container.querySelector('canvas');
                formData.append(`answers[${questionId}]`, canvas.toDataURL());
            }
        });

        // Submit answers
        fetch('api/submit_quiz.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Quiz completed! Your score: ${data.score}%`);
                window.location.href = 'dashboard.php';
            } else {
                alert('Error submitting quiz. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting quiz. Please try again.');
        });
    });
    </script>
</body>
</html>
