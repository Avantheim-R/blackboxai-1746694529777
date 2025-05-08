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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lupa Password - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="/assets/css/style.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="professional-pattern min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="logo-container w-20 h-20 mb-4">
                    <div class="logo-hex"></div>
                    <div class="logo-inner">
                        <span>@</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white tracking-tight">DesignHive</h1>
                <p class="text-blue-100 mt-2">SMK Negeri 1 Bantul</p>
            </a>
        </div>

        <!-- Forgot Password Card -->
        <div class="card-glass rounded-2xl p-8 shadow-professional hover-lift">
            <div class="text-center mb-8">
                <div class="icon-container mx-auto mb-4 w-16 h-16">
                    <i class="fas fa-key text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Lupa Password?</h2>
                <p class="text-blue-100 mt-2">Masukkan NIS dan email Anda untuk mereset password</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error rounded-lg px-4 py-3 mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-success rounded-lg px-4 py-3 mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($success) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-100 mb-2">NIS</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-100">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-yellow-400 transition-all duration-300"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-blue-100 mb-2">Email</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-100">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-yellow-400 transition-all duration-300"
                               placeholder="Masukkan email">
                    </div>
                </div>

                <button type="submit" 
                        class="btn-primary w-full py-3 flex items-center justify-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Link Reset</span>
                </button>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-blue-900/30 text-blue-100">atau</span>
                    </div>
                </div>

                <div class="text-center">
                    <a href="login.php" 
                       class="inline-flex items-center text-blue-100 hover:text-yellow-400 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="/" class="text-blue-100 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-10 left-10 w-64 h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob"></div>
        <div class="absolute top-10 right-10 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-10 left-20 w-64 h-64 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob animation-delay-4000"></div>
    </div>
</body>
</html>
