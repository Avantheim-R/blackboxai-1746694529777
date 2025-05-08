<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();
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
</head>
<body class="professional-pattern min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center fade-out">
        <!-- Logo -->
        <div class="mb-8">
            <div class="icon-container mx-auto mb-4 w-16 h-16 text-3xl animate-glow">
                <i class="fas fa-hexagon"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">DesignHive</h1>
            <p class="text-blue-200 mt-2">SMK Negeri 1 Bantul</p>
        </div>

        <!-- Logout Card -->
        <div class="card-glass rounded-2xl p-8 shadow-professional">
            <div class="flex flex-col items-center relative">
                <!-- Animated Circle -->
                <div class="relative w-16 h-16 mb-6">
                    <div class="absolute inset-0 border-4 border-[#FFB302]/20 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-[#FFB302] rounded-full border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-[#FFB302]">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-white mb-4">Sedang Keluar...</h2>
                <p class="text-blue-200">
                    Anda akan dialihkan ke halaman login dalam beberapa detik.
                </p>

                <!-- Progress Bar -->
                <div class="w-full h-1 bg-white/10 rounded-full mt-8 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-[#FFB302] to-[#F7CE68] animate-progress"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-10 left-10 w-64 h-64 bg-[#FFB302] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob"></div>
        <div class="absolute top-10 right-10 w-64 h-64 bg-[#2563EB] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-10 left-20 w-64 h-64 bg-[#F7CE68] rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes progress {
            from { width: 0; }
            to { width: 100%; }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }
        .animate-progress {
            animation: progress 2s linear;
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
            animation-delay: 1.8s;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    <script>
        // Redirect after animation
        setTimeout(function() {
            window.location.href = 'login.php?message=logged_out';
        }, 2000);
    </script>
</body>
</html>
