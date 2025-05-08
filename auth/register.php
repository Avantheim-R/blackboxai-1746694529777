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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="/assets/css/style.css" rel="stylesheet" />
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
                <h1 class="text-3xl font-bold text-white">DesignHive</h1>
                <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
            </a>
        </div>

        <!-- Register Card -->
        <div class="card-glass rounded-2xl p-8 shadow-professional hover-lift">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white">Daftar Akun Baru</h2>
                <p class="text-blue-200 mt-2">Mulai perjalanan desainmu bersama DesignHive</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100/90 backdrop-blur-sm border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100/90 backdrop-blur-sm border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-center text-sm"><?= htmlspecialchars($success) ?></p>
                    <a href="login.php" class="block mt-2 text-center text-green-700 underline">Klik di sini untuk login</a>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6" id="registerForm">
                <div>
                    <label for="nis" class="block text-sm font-medium text-blue-200 mb-2">NIS</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" id="nis" name="nis" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-[#FFB302] transition-all duration-300"
                               placeholder="Masukkan NIS">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-blue-200 mb-2">Nama Lengkap</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="name" name="name" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-[#FFB302] transition-all duration-300"
                               placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-blue-200 mb-2">Email</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-[#FFB302] transition-all duration-300"
                               placeholder="Masukkan email">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-blue-200 mb-2">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-[#FFB302] transition-all duration-300"
                               placeholder="Buat password">
                    </div>
                    <div class="mt-2 text-sm text-blue-200/70 space-y-1">
                        <p class="flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-[#FFB302]"></i>
                            Password harus memenuhi kriteria:
                        </p>
                        <ul class="list-none space-y-1 pl-6">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-xs mr-2 text-[#FFB302]"></i>
                                Minimal 8 karakter
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-xs mr-2 text-[#FFB302]"></i>
                                Mengandung huruf dan angka
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-xs mr-2 text-[#FFB302]"></i>
                                Mengandung karakter khusus (@$!%*#?&)
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-blue-200 mb-2">Konfirmasi Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-200">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="input-professional w-full pl-10 pr-4 py-3 group-hover:border-[#FFB302] transition-all duration-300"
                               placeholder="Masukkan ulang password">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" required
                           class="h-4 w-4 rounded border-white/10 bg-white/5 text-[#FFB302] focus:ring-[#FFB302]/50">
                    <label for="terms" class="ml-2 block text-sm text-blue-200">
                        Saya setuju dengan <a href="#" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors">syarat dan ketentuan</a>
                    </label>
                </div>

                <button type="submit" 
                        class="btn-primary w-full py-3 flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Daftar</span>
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
                        Sudah punya akun? 
                        <a href="login.php" class="text-[#FFB302] hover:text-[#F7CE68] transition-colors font-medium">
                            Masuk di sini
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
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-10 left-10 w-64 h-64 bg-[#FFB302] rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob"></div>
        <div class="absolute top-10 right-10 w-64 h-64 bg-[#2563EB] rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-10 left-20 w-64 h-64 bg-[#F7CE68] rounded-full mix-blend-multiply filter blur-xl opacity-5 animate-blob animation-delay-4000"></div>
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
