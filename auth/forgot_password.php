<?php
session_start();
require_once('../config/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'] ?? '';
    
    if (empty($nis)) {
        $error = 'Silakan masukkan NIS Anda';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE nis = ?");
            $stmt->execute([$nis]);
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
                // For demo, just show the reset link
                $success = "Link reset password telah dikirim ke email Anda.";
            } else {
                $error = 'NIS tidak ditemukan';
            }
        } catch (Exception $e) {
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
    <title>Lupa Password - Sistem Pembelajaran Desain Grafis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Lupa Password</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Masukkan NIS Anda untuk mereset password
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div>
                    <label for="nis" class="sr-only">NIS</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input id="nis" name="nis" type="text" required
                               class="appearance-none rounded-lg relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="Nomor Induk Siswa (NIS)">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-key text-indigo-500 group-hover:text-indigo-400"></i>
                        </span>
                        Reset Password
                    </button>
                </div>

                <div class="text-sm text-center">
                    <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
