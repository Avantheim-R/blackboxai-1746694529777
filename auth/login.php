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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk - DesignHive</title>
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

        <!-- Login Card -->
        <div class="card-glass rounded-2xl p-8 shadow-professional hover-lift">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Selamat Datang Kembali!</h2>
                <p class="text-blue-100 mt-2">Masuk untuk melanjutkan perjalanan desainmu</p>
            </div>

            <?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                <div class="alert-success rounded-lg px-4 py-3 mb-6">
                    <p class="text-center text-sm">Anda telah berhasil keluar dari sistem</p>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-error rounded-lg px-4 py-3 mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-100 mb-2">NIS</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-100">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-yellow-400 transition-all duration-300"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-blue-100 mb-2">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-100">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-yellow-400 transition-all duration-300"
                               placeholder="Masukkan password">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                               class="h-4 w-4 rounded border-white/10 bg-white/5 text-yellow-400 focus:ring-yellow-400/50">
                        <label for="remember" class="ml-2 block text-blue-100">
                            Ingat saya
                        </label>
                    </div>
                    <a href="forgot_password.php" class="text-yellow-400 hover:text-yellow-300 transition-colors">
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
                        <span class="px-2 bg-blue-900/30 text-blue-100">atau</span>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-blue-100">
                        Belum punya akun? 
                        <a href="register.php" class="text-yellow-400 hover:text-yellow-300 transition-colors font-medium">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="/" class="text-blue-100 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
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
