<?php
session_start();
require_once('../config/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nis) || empty($name) || empty($password) || empty($confirm_password)) {
        $error = 'Silakan isi semua field';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } else {
        // Check if NIS already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE nis = ?");
        $stmt->execute([$nis]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'NIS sudah terdaftar';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO Users (nis, name, password, role, level, points, created_at) 
                    VALUES (?, ?, ?, 'student', 1, 0, NOW())
                ");
                $stmt->execute([
                    $nis,
                    $name,
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 2px rgba(78, 205, 196, 0.2);
        }
        .btn-register {
            background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(78, 205, 196, 0.3);
        }
        .logo-container {
            position: relative;
            margin-bottom: 2rem;
        }
        .logo-container::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 100%);
            border-radius: 3px;
        }
        .password-requirements {
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 0.5rem;
        }
        .password-requirements i {
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="px-4">
    <div class="register-card">
        <div class="logo-container text-center">
            <a href="/" class="inline-block">
                <i class="fas fa-hexagon text-4xl text-gray-800"></i>
                <h1 class="text-2xl font-bold text-gray-800 mt-2">DesignHive</h1>
            </a>
            <p class="text-gray-600 text-sm mt-2">SMK Negeri 1 Bantul</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?= htmlspecialchars($success) ?></span>
                <a href="login.php" class="block mt-2 text-green-700 underline">Klik di sini untuk login</a>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                <input type="text" id="nis" name="nis" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan NIS">
                <p class="text-sm text-gray-500 mt-1">Gunakan NIS yang terdaftar di sekolah</p>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="name" name="name" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan nama lengkap">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Buat password">
                <div class="password-requirements">
                    <p><i class="fas fa-info-circle"></i> Password harus memenuhi kriteria berikut:</p>
                    <ul class="list-disc list-inside ml-4">
                        <li>Minimal 8 karakter</li>
                        <li>Mengandung huruf dan angka</li>
                        <li>Mengandung minimal 1 karakter khusus</li>
                    </ul>
                </div>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan ulang password">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="terms" name="terms" required
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    Saya setuju dengan <a href="#" class="text-indigo-600 hover:text-indigo-500">syarat dan ketentuan</a>
                </label>
            </div>

            <button type="submit" class="btn-register w-full flex justify-center py-2 px-4 rounded-md shadow-sm text-sm font-medium">
                Daftar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun? 
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>

    <div class="fixed bottom-4 left-4">
        <a href="/" class="text-white hover:text-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Beranda
        </a>
    </div>

    <script>
    // Password validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            alert('Password tidak cocok!');
        }

        if (passwordInput.value.length < 8) {
            e.preventDefault();
            alert('Password harus minimal 8 karakter!');
        }

        // Check for letters and numbers
        if (!/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/.test(passwordInput.value)) {
            e.preventDefault();
            alert('Password harus mengandung huruf, angka, dan karakter khusus!');
        }
    });
    </script>
</body>
</html>
