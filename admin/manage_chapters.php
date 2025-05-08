<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Bab - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <nav class="bg-gray-800 w-64 px-4 py-6">
        <div class="text-white text-xl font-semibold mb-8">Admin Panel</div>
        <ul>
            <li class="mb-4">
                <a href="dashboard.php" class="text-gray-300 hover:text-white">Dashboard</a>
            </li>
            <li class="mb-4">
                <a href="manage_chapters.php" class="text-white bg-gray-700 px-2 py-1 rounded">Manajemen Bab</a>
            </li>
            <li class="mb-4">
                <a href="manage_users.php" class="text-gray-300 hover:text-white">Manajemen User</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Manajemen Bab dan Sub Bab</h1>
            <button onclick="openAddChapterModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <i class="fas fa-plus mr-2"></i> Tambah Bab Baru
            </button>
        </div>

        <!-- Chapters List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div id="chapters-list">
                <?php
                $stmt = $pdo->query("SELECT * FROM Chapters ORDER BY order_number");
                while ($chapter = $stmt->fetch()) {
                    ?>
                    <div class="mb-6 border rounded-lg p-4" id="chapter-<?= $chapter['chapter_id'] ?>">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold"><?= htmlspecialchars($chapter['title']) ?></h2>
                            <div class="space-x-2">
                                <button onclick="editChapter(<?= $chapter['chapter_id'] ?>)" 
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="addSubChapter(<?= $chapter['chapter_id'] ?>)"
                                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                    <i class="fas fa-plus"></i> Tambah Sub Bab
                                </button>
                            </div>
                        </div>

                        <!-- Sub Chapters -->
                        <div class="ml-8">
                            <?php
                            $subStmt = $pdo->prepare("SELECT * FROM SubChapters WHERE chapter_id = ? ORDER BY order_number");
                            $subStmt->execute([$chapter['chapter_id']]);
                            while ($subChapter = $subStmt->fetch()) {
                                ?>
                                <div class="mb-4 border-l-2 border-gray-200 pl-4" id="subchapter-<?= $subChapter['subchapter_id'] ?>">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg"><?= htmlspecialchars($subChapter['title']) ?></h3>
                                        <div class="space-x-2">
                                            <button onclick="editContent(<?= $subChapter['subchapter_id'] ?>)"
                                                    class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600">
                                                <i class="fas fa-file-alt"></i> Edit Konten
                                            </button>
                                            <button onclick="editSubChapter(<?= $subChapter['subchapter_id'] ?>)"
                                                    class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button onclick="deleteSubChapter(<?= $subChapter['subchapter_id'] ?>)"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Chapter Modal -->
<div id="chapterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium" id="chapterModalTitle">Tambah Bab Baru</h3>
            <form id="chapterForm" class="mt-4">
                <input type="hidden" id="chapterId" name="chapter_id" value="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="chapterTitle">
                        Judul Bab
                    </label>
                    <input type="text" id="chapterTitle" name="title" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="chapterDescription">
                        Deskripsi
                    </label>
                    <textarea id="chapterDescription" name="description" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeChapterModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Content Editor Modal -->
<div id="contentEditorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-3/4 h-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium mb-4">Edit Konten Materi</h3>
            <textarea id="contentEditor"></textarea>
            <div class="flex justify-end space-x-2 mt-4">
                <button onclick="closeContentModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Batal
                </button>
                <button onclick="saveContent()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize TinyMCE
tinymce.init({
    selector: '#contentEditor',
    height: '500px',
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic backcolor | \
             alignleft aligncenter alignright alignjustify | \
             bullist numlist outdent indent | removeformat | help',
    images_upload_url: 'upload.php',
    automatic_uploads: true
});

// Chapter Modal Functions
function openAddChapterModal() {
    document.getElementById('chapterModalTitle').textContent = 'Tambah Bab Baru';
    document.getElementById('chapterId').value = '';
    document.getElementById('chapterTitle').value = '';
    document.getElementById('chapterDescription').value = '';
    document.getElementById('chapterModal').classList.remove('hidden');
}

function closeChapterModal() {
    document.getElementById('chapterModal').classList.add('hidden');
}

// Content Editor Modal Functions
function openContentEditor(subchapterId) {
    // Fetch existing content
    fetch(`get_content.php?subchapter_id=${subchapterId}`)
        .then(response => response.json())
        .then(data => {
            tinymce.get('contentEditor').setContent(data.content);
            document.getElementById('contentEditorModal').classList.remove('hidden');
        });
}

function closeContentModal() {
    document.getElementById('contentEditorModal').classList.add('hidden');
}

function saveContent() {
    const content = tinymce.get('contentEditor').getContent();
    // Save content using fetch
    // ...
    closeContentModal();
}

// Add event listeners
document.getElementById('chapterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle form submission
    // ...
});
</script>

</body>
</html>
