<?php
session_start();
require_once('../config/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nis) || empty($password)) {
        $error = 'Silakan isi semua field';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE nis = ?");
        $stmt->execute([$nis]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Update last login
            $stmt = $pdo->prepare("UPDATE Users SET last_login = NOW() WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);

            $redirect = $user['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = 'NIS atau password salah';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="professional-pattern min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="icon-container mx-auto mb-4 w-16 h-16 text-3xl animate-glow">
                    <i class="fas fa-hexagon"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">DesignHive</h1>
                <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
            </a>
        </div>

        <!-- Login Card -->
        <div class="card-glass rounded-2xl p-8 shadow-professional hover-lift">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Selamat Datang Kembali!</h2>
                <p class="text-blue-200 mt-2">Masuk untuk melanjutkan perjalanan desainmu</p>
            </div>

            <?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm">Anda telah berhasil keluar dari sistem</p>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-200 mb-2">NIS</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="input-professional w-full pl-10 pr-4 py-3"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-blue-200 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="input-professional w-full pl-10 pr-4 py-3"
                               placeholder="Masukkan password">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                               class="h-4 w-4 rounded border-white/10 bg-white/5 text-[#FFB302] focus:ring-[#FFB302]/50">
                        <label for="remember" class="ml-2 block text-blue-200">
                            Ingat saya
                        </label>
                    </div>
                    <a href="forgot_password.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" 
                        class="btn-primary w-full py-3 flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk</span>
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
                    <p class="text-blue-200">
                        Belum punya akun? 
                        <a href="register.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors font-medium">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="/" class="text-blue-200 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-10 left-10 w-64 h-64 bg-[#FFB302] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob"></div>
        <div class="absolute top-10 right-10 w-64 h-64 bg-[#2563EB] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-10 left-20 w-64 h-64 bg-[#F7CE68] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>
</html>
