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
    <style>
        .honeycomb-bg {
            background-color: #1E3A8A;
            background-image: url("data:image/svg+xml,%3Csvg width='56' height='100' viewBox='0 0 56 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M28 66L0 50L0 16L28 0L56 16L56 50L28 66L28 100' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3Cpath d='M28 0L28 34L0 50L0 84L28 100L56 84L56 50L28 34' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3C/svg%3E");
        }
        .login-card {
            animation: fadeInUp 0.8s ease-out;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .input-glow:focus {
            box-shadow: 0 0 15px rgba(255, 179, 2, 0.3);
        }
    </style>
</head>
<body class="honeycomb-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <i class="fas fa-hexagon text-[#FFB302] text-5xl animate-pulse"></i>
                <h1 class="text-3xl font-bold text-white mt-4">DesignHive</h1>
                <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
            </a>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Selamat Datang Kembali!</h2>

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
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
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
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
                               placeholder="Masukkan password">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                               class="h-4 w-4 rounded border-white/20 bg-white/10 text-[#FFB302] focus:ring-[#FFB302]/50">
                        <label for="remember" class="ml-2 block text-blue-200">
                            Ingat saya
                        </label>
                    </div>
                    <a href="forgot_password.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#FFB302] to-[#F7CE68] text-blue-900 py-3 rounded-lg font-semibold hover:shadow-[0_0_20px_rgba(255,179,2,0.4)] transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-blue-200">
                    Belum punya akun? 
                    <a href="register.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors font-medium">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-8 text-center">
            <a href="/" class="text-blue-200 hover:text-white transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
