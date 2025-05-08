<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with message after showing animation
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1E3A8A;
            background-image: radial-gradient(rgba(255, 179, 2, 0.05) 2px, transparent 2px);
            background-size: 32px 32px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 3px solid rgba(255, 179, 2, 0.1);
            border-radius: 50%;
            border-top-color: #FFB302;
            animation: spin 1s linear infinite;
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
            animation-delay: 1.5s;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center fade-out">
        <!-- Logo -->
        <div class="mb-8">
            <i class="fas fa-hexagon text-[#FFB302] text-5xl"></i>
            <h1 class="text-3xl font-bold text-white mt-4">DesignHive</h1>
            <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
        </div>

        <!-- Logout Card -->
        <div class="bg-blue-900/30 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/10">
            <div class="flex flex-col items-center">
                <div class="loading-spinner mb-6"></div>
                <h2 class="text-2xl font-bold text-white mb-4">Sedang Keluar...</h2>
                <p class="text-blue-200">
                    Anda akan dialihkan ke halaman login dalam beberapa detik.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Redirect after animation
        setTimeout(function() {
            window.location.href = 'login.php?message=logged_out';
        }, 2000);
    </script>
</body>
</html>
