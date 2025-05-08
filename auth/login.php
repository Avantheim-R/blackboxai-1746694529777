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

        <!-- Login Card -->
        <div class="bg-blue-900/30 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/10">
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
                               class="form-input w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none"
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
                               class="form-input w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none"
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
                        class="w-full bg-[#FFB302] text-blue-900 py-3 rounded-lg font-semibold hover:bg-[#F7CE68] transition-all duration-300 flex items-center justify-center space-x-2">
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
