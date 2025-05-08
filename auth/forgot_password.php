<?php
session_start();
require_once('../config/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($nis) || empty($email)) {
        $error = 'Silakan isi semua field';
    } else {
        // Check if NIS and email match
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE nis = ? AND email = ?");
        $stmt->execute([$nis, $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("
                UPDATE Users 
                SET reset_token = ?, reset_token_expires = ?
                WHERE user_id = ?
            ");
            $stmt->execute([$token, $expires, $user['user_id']]);

            // In a real application, send email with reset link
            // For demo purposes, just show success message
            $success = 'Link reset password telah dikirim ke email Anda. Silakan cek inbox/spam.';
        } else {
            $error = 'NIS atau email tidak ditemukan';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1E3A8A;
            background-image: radial-gradient(rgba(255, 179, 2, 0.05) 2px, transparent 2px);
            background-size: 32px 32px;
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #FFB302;
            box-shadow: 0 0 0 2px rgba(255, 179, 2, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <i class="fas fa-hexagon text-[#FFB302] text-5xl"></i>
                <h1 class="text-3xl font-bold text-white mt-4">DesignHive</h1>
                <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
            </a>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-blue-900/30 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/10">
            <div class="text-center mb-8">
                <i class="fas fa-key text-[#FFB302] text-4xl mb-4"></i>
                <h2 class="text-2xl font-bold text-white">Lupa Password?</h2>
                <p class="text-blue-200 mt-2">
                    Masukkan NIS dan email Anda untuk mereset password
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-200 mb-2">NIS</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="form-input w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-blue-200 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" required
                               class="form-input w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none"
                               placeholder="Masukkan email">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-[#FFB302] text-blue-900 py-3 rounded-lg font-semibold hover:bg-[#F7CE68] transition-all duration-300 flex items-center justify-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Link Reset</span>
                </button>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-blue-900/30 text-blue-200">atau</span>
                    </div>
                </div>

                <div class="text-center">
                    <a href="login.php" 
                       class="inline-flex items-center text-blue-200 hover:text-[#FFB302] transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="/" class="text-blue-200 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
