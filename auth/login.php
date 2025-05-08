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
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
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
        .btn-login {
            background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
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
    </style>
</head>
<body class="px-4">
    <div class="login-card">
        <div class="logo-container text-center">
            <a href="/" class="inline-block">
                <i class="fas fa-hexagon text-4xl text-gray-800"></i>
                <h1 class="text-2xl font-bold text-gray-800 mt-2">DesignHive</h1>
            </a>
            <p class="text-gray-600 text-sm mt-2">SMK Negeri 1 Bantul</p>
        </div>

<?php 
    $message = $_GET['message'] ?? '';
    if ($message === 'logged_out'): 
?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline">Anda telah berhasil keluar dari sistem</span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
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
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="form-input mt-1 block w-full rounded-md shadow-sm px-4 py-2"
                       placeholder="Masukkan password">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Ingat saya
                    </label>
                </div>

                <a href="forgot_password.php" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Lupa password?
                </a>
            </div>

            <button type="submit" class="btn-login w-full flex justify-center py-2 px-4 rounded-md shadow-sm text-sm font-medium">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center">
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
</body>
</html>
