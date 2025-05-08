<?php
session_start();
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Ensure user is a student
if (!isStudent()) {
    header('Location: /auth/login.php');
    exit;
}

$exam_id = $_GET['exam_id'] ?? null;
if (!$exam_id) {
    header('Location: dashboard.php');
    exit;
}

// Get exam and certificate details
$stmt = $pdo->prepare("
    SELECT e.*, c.certificate_pdf, u.name 
    FROM FinalExam e
    JOIN Users u ON e.user_id = u.user_id
    LEFT JOIN Certificates c ON e.exam_id = c.exam_id
    WHERE e.exam_id = ? AND e.user_id = ?
");
$stmt->execute([$exam_id, $_SESSION['user_id']]);
$result = $stmt->fetch();

if (!$result) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="dashboard.php" class="text-xl font-bold text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
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

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Sertifikat Kelulusan</h1>
                <p class="text-gray-600 mt-2">
                    Selamat atas pencapaian Anda dalam kursus Desain Grafis!
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Nama</p>
                        <p class="font-semibold"><?= htmlspecialchars($result['name']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal Ujian</p>
                        <p class="font-semibold"><?= date('d F Y', strtotime($result['created_at'])) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Nilai Akhir</p>
                        <p class="font-semibold"><?= number_format($result['score'], 1) ?>%</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="font-semibold <?= $result['score'] >= 70 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $result['score'] >= 70 ? 'Lulus' : 'Tidak Lulus' ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($result['score'] >= 70 && $result['certificate_pdf']): ?>
                <div class="mb-6">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="/uploads/certificates/<?= htmlspecialchars($result['certificate_pdf']) ?>" 
                                class="w-full h-[600px] border rounded-lg"></iframe>
                    </div>
                </div>

                <div class="flex justify-center space-x-4">
                    <a href="/uploads/certificates/<?= htmlspecialchars($result['certificate_pdf']) ?>" 
                       download="Sertifikat_<?= htmlspecialchars($result['name']) ?>.pdf"
                       class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-download mr-2"></i>
                        Download Sertifikat
                    </a>
                    <button onclick="shareCertificate()"
                            class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <i class="fas fa-share-alt mr-2"></i>
                        Bagikan
                    </button>
                </div>
            <?php elseif ($result['score'] < 70): ?>
                <div class="text-center">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-exclamation-circle text-5xl"></i>
                    </div>
                    <p class="text-gray-800 mb-4">
                        Maaf, Anda belum mencapai nilai minimum untuk mendapatkan sertifikat.
                        Nilai minimum yang dibutuhkan adalah 70%.
                    </p>
                    <a href="final_exam.php" 
                       class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-redo mr-2"></i>
                        Coba Lagi
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <div class="animate-spin text-blue-600 mb-4">
                        <i class="fas fa-circle-notch text-5xl"></i>
                    </div>
                    <p class="text-gray-800">
                        Sertifikat Anda sedang diproses. Silakan refresh halaman ini dalam beberapa saat.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function shareCertificate() {
        if (navigator.share) {
            navigator.share({
                title: 'Sertifikat Kursus Desain Grafis',
                text: 'Saya telah menyelesaikan kursus Desain Grafis dengan nilai <?= number_format($result['score'], 1) ?>%!',
                url: window.location.href
            })
            .catch(error => console.log('Error sharing:', error));
        } else {
            alert('Maaf, fitur berbagi tidak didukung di browser Anda.');
        }
    }

    // Auto refresh if certificate is processing
    <?php if ($result['score'] >= 70 && !$result['certificate_pdf']): ?>
    setTimeout(() => {
        location.reload();
    }, 10000); // Refresh every 10 seconds
    <?php endif; ?>
    </script>
</body>
</html>
