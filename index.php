<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['user_role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php';
    header("Location: $redirect");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DesignHive - Platform Pembelajaran DKV SMK Negeri 1 Bantul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="/assets/css/style.css" rel="stylesheet" />
    <style>
        .honeycomb-bg {
            background-color: #1E3A8A;
            background-image: url("data:image/svg+xml,%3Csvg width='56' height='100' viewBox='0 0 56 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M28 66L0 50L0 16L28 0L56 16L56 50L28 66L28 100' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3Cpath d='M28 0L28 34L0 50L0 84L28 100L56 84L56 50L28 34' fill='none' stroke='%23FFB302' stroke-opacity='0.15' stroke-width='2'/%3E%3C/svg%3E");
        }
        .hero-glow {
            position: relative;
        }
        .hero-glow::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,179,2,0.2) 0%, rgba(30,58,138,0) 70%);
            z-index: -1;
        }
        .feature-card {
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,179,2,0.1) 0%, rgba(255,255,255,0.05) 100%);
            z-index: -1;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="honeycomb-bg min-h-screen">
    <nav class="fixed w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-hexagon text-[#FFB302] text-2xl mr-2 animate-pulse"></i>
                    <span class="text-2xl font-bold text-white">DesignHive</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="auth/login.php" class="text-white hover:text-[#FFB302] transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </a>
                    <a href="auth/register.php" 
                       class="bg-[#FFB302] text-blue-900 px-6 py-2 rounded-full hover:bg-[#F7CE68] transition font-semibold">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <header class="min-h-screen flex items-center justify-center text-center px-4 hero-glow">
        <div class="max-w-4xl animate-fade-in">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 text-white animate-float">
                Selamat Datang di 
                <span class="text-[#FFB302]">DesignHive</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-200">
                Platform pembelajaran digital untuk siswa DKV SMK Negeri 1 Bantul.
                <span class="block mt-2">Belajar desain grafis kapan saja, di mana saja.</span>
            </p>
            <button onclick="window.location.href='auth/register.php'" 
                    class="bg-gradient-to-r from-[#FFB302] to-[#F7CE68] text-blue-900 px-8 py-4 rounded-full text-lg font-semibold hover:shadow-[0_0_30px_rgba(255,179,2,0.5)] transform hover:-translate-y-1 transition duration-300">
                Mulai Belajar Sekarang
            </button>
        </div>
    </header>

    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16 text-white">
                Fitur <span class="text-[#FFB302]">Unggulan</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card glass p-8 rounded-xl text-center transform transition duration-300">
                    <i class="fas fa-book-open text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Materi Terstruktur</h3>
                    <p class="text-blue-200">Kurikulum yang dirancang khusus sesuai kebutuhan industri DKV</p>
                </div>
                <div class="feature-card glass p-8 rounded-xl text-center transform transition duration-300">
                    <i class="fas fa-tasks text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Latihan Interaktif</h3>
                    <p class="text-blue-200">Praktik langsung dengan tools desain dan feedback instant</p>
                </div>
                <div class="feature-card glass p-8 rounded-xl text-center transform transition duration-300">
                    <i class="fas fa-users text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Forum Diskusi</h3>
                    <p class="text-blue-200">Diskusi dengan guru dan sesama siswa DKV</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-blue-900/50">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-16 text-white">
                Bergabung dengan <span class="text-[#FFB302]">DesignHive</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
                <div class="glass p-8 rounded-xl animate-fade-in" style="animation-delay: 0.2s">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">1000+</div>
                    <div class="text-blue-200">Siswa Aktif</div>
                </div>
                <div class="glass p-8 rounded-xl animate-fade-in" style="animation-delay: 0.4s">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">50+</div>
                    <div class="text-blue-200">Materi Desain</div>
                </div>
                <div class="glass p-8 rounded-xl animate-fade-in" style="animation-delay: 0.6s">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">24/7</div>
                    <div class="text-blue-200">Akses Belajar</div>
                </div>
                <div class="glass p-8 rounded-xl animate-fade-in" style="animation-delay: 0.8s">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">100%</div>
                    <div class="text-blue-200">Online</div>
                </div>
            </div>
            <a href="auth/register.php" 
               class="bg-gradient-to-r from-[#FFB302] to-[#F7CE68] text-blue-900 px-8 py-4 rounded-full text-lg font-semibold hover:shadow-[0_0_30px_rgba(255,179,2,0.5)] transform hover:-translate-y-1 transition duration-300 inline-block">
                Mulai Perjalanan Desainmu
            </a>
        </div>
    </section>

    <footer class="bg-blue-900/50 text-blue-200 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-8 md:mb-0 flex items-center">
                    <i class="fas fa-hexagon text-[#FFB302] text-3xl mr-2"></i>
                    <span class="text-2xl font-bold text-white">DesignHive</span>
                </div>
                <div class="space-x-6">
                    <a href="#" class="text-2xl hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-2xl hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-2xl hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="mt-8 text-sm">
                &copy; <?= date('Y') ?> DesignHive - Platform Pembelajaran DKV SMK Negeri 1 Bantul
            </div>
        </div>
    </footer>
</body>
</html>
