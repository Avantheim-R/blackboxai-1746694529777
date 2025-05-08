<?php
session_start();
require_once('../config/db.php');

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['user_role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php';
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nis) || empty($password)) {
        $error = 'Silakan isi semua field';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE nis = ?");
            $stmt->execute([$nis]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect based on role
                $redirect = $user['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php';
                header("Location: $redirect");
                exit;
            } else {
                $error = 'NIS atau password salah';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Selamat Datang
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sistem Pembelajaran Desain Grafis Interaktif
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="nis" class="sr-only">NIS</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="nis" name="nis" type="text" required 
                                class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                placeholder="Nomor Induk Siswa (NIS)">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required 
                                class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                placeholder="Password">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        Masuk
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Belum punya akun? Daftar
                        </a>
                    </div>
                    <div class="text-sm">
                        <a href="forgot_password.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Lupa password?
                        </a>
                    </div>
                </div>
            </form>

            <!-- Quick Login for Demo -->
            <?php if ($_SERVER['SERVER_NAME'] === 'localhost'): ?>
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="text-sm text-gray-600 mb-4">Demo Quick Login:</div>
                <div class="space-y-2">
                    <button onclick="quickLogin('admin')" class="w-full px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded">
                        Login as Admin (NIS: admin123)
                    </button>
                    <button onclick="quickLogin('student')" class="w-full px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded">
                        Login as Student (NIS: student123)
                    </button>
                </div>
            </div>
            <script>
            function quickLogin(type) {
                document.getElementById('nis').value = type + '123';
                document.getElementById('password').value = 'password123';
                document.querySelector('form').submit();
            }
            </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
