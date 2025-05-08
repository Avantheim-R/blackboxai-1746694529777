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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($nis) || empty($name) || empty($password) || empty($confirm_password)) {
        $error = 'Silakan isi semua field';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        try {
            // Check if NIS already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE nis = ?");
            $stmt->execute([$nis]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'NIS sudah terdaftar';
            } else {
                // Create new user
                $stmt = $pdo->prepare("
                    INSERT INTO Users (nis, name, password, role) 
                    VALUES (?, ?, ?, 'student')
                ");
                $stmt->execute([
                    $nis,
                    $name,
                    password_hash($password, PASSWORD_DEFAULT)
                ]);

                $success = 'Pendaftaran berhasil! Silakan login.';
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
    <title>Registrasi - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Daftar Akun Baru
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sistem Pembelajaran Desain Grafis Interaktif
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($success) ?></span>
                    <div class="mt-2">
                        <a href="login.php" class="font-medium text-green-600 hover:text-green-500">
                            Klik di sini untuk login
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <form class="mt-8 space-y-6" method="POST">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="nis" class="sr-only">NIS</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input id="nis" name="nis" type="text" required 
                                    class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                    placeholder="Nomor Induk Siswa (NIS)">
                            </div>
                        </div>
                        <div>
                            <label for="name" class="sr-only">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input id="name" name="name" type="text" required 
                                    class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                    placeholder="Nama Lengkap">
                            </div>
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" required 
                                    class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                    placeholder="Password">
                            </div>
                        </div>
                        <div>
                            <label for="confirm_password" class="sr-only">Konfirmasi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="confirm_password" name="confirm_password" type="password" required 
                                    class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                    placeholder="Konfirmasi Password">
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Daftar
                        </button>
                    </div>

                    <div class="text-sm text-center">
                        <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Sudah punya akun? Login di sini
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Client-side password validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter');
        } else if (password !== confirmPassword) {
            e.preventDefault();
            alert('Password tidak cocok');
        }
    });
    </script>
</body>
</html>
