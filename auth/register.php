<?php
session_start();
require_once('../config/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nis) || empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Silakan isi semua field';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter';
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $password)) {
        $error = 'Password harus mengandung huruf, angka, dan karakter khusus';
    } else {
        // Check if NIS already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE nis = ?");
        $stmt->execute([$nis]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'NIS sudah terdaftar';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO Users (nis, name, email, password, role, level, points, created_at) 
                    VALUES (?, ?, ?, ?, 'student', 1, 0, NOW())
                ");
                $stmt->execute([
                    $nis,
                    $name,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT)
                ]);
                $success = 'Pendaftaran berhasil! Silakan login.';
            } catch (PDOException $e) {
                $error = 'Terjadi kesalahan saat mendaftar';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .honeycomb-bg {
            background-color: #1E3A8A;
            background-image: url("data:image/svg+xml,%3Csvg width='56' height='100' viewBox='0 0 56 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M28 66L0 50L0 16L28 0L56 16L56 50L28 66L28 100' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3Cpath d='M28 0L28 34L0 50L0 84L28 100L56 84L56 50L28 34' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3C/svg%3E");
        }
        .register-card {
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

        <!-- Register Card -->
        <div class="register-card rounded-2xl p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Daftar Akun Baru</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($success) ?></p>
                    <a href="login.php" class="block mt-2 text-center text-green-700 underline">Klik di sini untuk login</a>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6" id="registerForm">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-200 mb-2">NIS</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-blue-200 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="name" name="name" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
                               placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-blue-200 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
                               placeholder="Masukkan email">
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
                               placeholder="Buat password">
                    </div>
                    <div class="mt-2 text-sm text-blue-200/80 space-y-1">
                        <p class="flex items-center">
                            <i class="fas fa-info-circle mr-2 text-[#FFB302]"></i>
                            Password harus memenuhi kriteria:
                        </p>
                        <ul class="list-disc list-inside pl-5 space-y-1">
                            <li>Minimal 8 karakter</li>
                            <li>Mengandung huruf dan angka</li>
                            <li>Mengandung karakter khusus (@$!%*#?&)</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-blue-200 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200/70 focus:border-[#FFB302] focus:ring-2 focus:ring-[#FFB302]/50 transition-all input-glow"
                               placeholder="Masukkan ulang password">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" required
                           class="h-4 w-4 rounded border-white/20 bg-white/10 text-[#FFB302] focus:ring-[#FFB302]/50">
                    <label for="terms" class="ml-2 block text-sm text-blue-200">
                        Saya setuju dengan <a href="#" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors">syarat dan ketentuan</a>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#FFB302] to-[#F7CE68] text-blue-900 py-3 rounded-lg font-semibold hover:shadow-[0_0_20px_rgba(255,179,2,0.4)] transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-blue-200">
                    Sudah punya akun? 
                    <a href="login.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors font-medium">
                        Masuk di sini
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

    <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password tidak cocok!');
            return;
        }

        if (!passwordRegex.test(password)) {
            e.preventDefault();
            alert('Password harus memenuhi semua kriteria!');
            return;
        }
    });
    </script>
</body>
</html>
