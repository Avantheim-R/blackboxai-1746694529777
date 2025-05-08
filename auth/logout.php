<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with message
header("Location: login.php?message=logged_out");
exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluar - DesignHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .honeycomb-bg {
            background-color: #1E3A8A;
            background-image: url("data:image/svg+xml,%3Csvg width='56' height='100' viewBox='0 0 56 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M28 66L0 50L0 16L28 0L56 16L56 50L28 66L28 100' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3Cpath d='M28 0L28 34L0 50L0 84L28 100L56 84L56 50L28 34' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3C/svg%3E");
        }
        .logout-card {
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
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 179, 2, 0.3);
            border-radius: 50%;
            border-top-color: #FFB302;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="honeycomb-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <i class="fas fa-hexagon text-[#FFB302] text-5xl animate-pulse"></i>
            <h1 class="text-3xl font-bold text-white mt-4">DesignHive</h1>
            <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
        </div>

        <!-- Logout Card -->
        <div class="logout-card rounded-2xl p-8 shadow-2xl text-center">
            <div class="loading-spinner mx-auto mb-6"></div>
            <h2 class="text-2xl font-bold text-white mb-4">Sedang Keluar...</h2>
            <p class="text-blue-200 mb-6">
                Anda akan dialihkan ke halaman login dalam beberapa detik.
            </p>
        </div>
    </div>

    <script>
        // Redirect after showing loading animation
        setTimeout(function() {
            window.location.href = 'login.php?message=logged_out';
        }, 2000);
    </script>
</body>
</html>
