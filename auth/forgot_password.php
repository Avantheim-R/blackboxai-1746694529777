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
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .forgot-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
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
        .btn-submit {
            background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
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
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="px-4">
    <div class="forgot-card">
        <div class="logo-container text-center">
            <a href="/" class="inline-block">
                <i class="fas fa-hexagon text-4xl text-gray-800 animate-float"></i>
                <h1 class="text-2xl font-bold text-gray-800 mt-2">DesignHive</h1>
            </a>
            <p class="text-gray-600 text-sm mt-2">SMK Negeri 1 Bantul</p>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-xl font-semibold text-gray-800">Lupa Password?</h2>
            <p class="text-gray-600 text-sm mt-2">
                Masukkan NIS dan email Anda untuk mereset password
            </p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline"><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                <input type="text" id="nis" name="nis" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan NIS">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan email">
            </div>

            <button type="submit" class="btn-submit w-full flex justify-center py-2 px-4 rounded-md shadow-sm text-sm font-medium">
                Reset Password
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Ingat password Anda? 
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Masuk di sini
                </a>
            </p>
        </div>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Daftar sekarang
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
    // Simple form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const nis = document.getElementById('nis').value;
        const email = document.getElementById('email').value;

        if (!nis || !email) {
            e.preventDefault();
            alert('Silakan isi semua field!');
        }

        if (!email.includes('@')) {
            e.preventDefault();
            alert('Masukkan alamat email yang valid!');
        }
    });
    </script>
</body>
</html>
