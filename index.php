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
        .bg-dots {
            background-image: radial-gradient(rgba(255, 179, 2, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-[#1E3A8A] bg-dots min-h-screen font-[Poppins]">
    <nav class="fixed w-full z-50 bg-blue-900/50 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-hexagon text-[#FFB302] text-2xl mr-2"></i>
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

    <header class="min-h-screen flex items-center justify-center text-center px-4">
        <div class="max-w-4xl">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 text-white">
                Selamat Datang di 
                <span class="text-[#FFB302]">DesignHive</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-200">
                Platform pembelajaran digital untuk siswa DKV SMK Negeri 1 Bantul.
                <span class="block mt-2">Belajar desain grafis kapan saja, di mana saja.</span>
            </p>
            <button onclick="window.location.href='auth/register.php'" 
                    class="bg-[#FFB302] text-blue-900 px-8 py-4 rounded-full text-lg font-semibold hover:bg-[#F7CE68] transition transform hover:-translate-y-1 duration-300">
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
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl text-center border border-white/10 hover:-translate-y-2 transition duration-300">
                    <i class="fas fa-book-open text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Materi Terstruktur</h3>
                    <p class="text-blue-200">Kurikulum yang dirancang khusus sesuai kebutuhan industri DKV</p>
                </div>
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl text-center border border-white/10 hover:-translate-y-2 transition duration-300">
                    <i class="fas fa-tasks text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Latihan Interaktif</h3>
                    <p class="text-blue-200">Praktik langsung dengan tools desain dan feedback instant</p>
                </div>
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl text-center border border-white/10 hover:-translate-y-2 transition duration-300">
                    <i class="fas fa-users text-5xl text-[#FFB302] mb-6"></i>
                    <h3 class="text-xl font-semibold mb-4 text-white">Forum Diskusi</h3>
                    <p class="text-blue-200">Diskusi dengan guru dan sesama siswa DKV</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 bg-blue-900/30">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-16 text-white">
                Bergabung dengan <span class="text-[#FFB302]">DesignHive</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl border border-white/10">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">1000+</div>
                    <div class="text-blue-200">Siswa Aktif</div>
                </div>
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl border border-white/10">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">50+</div>
                    <div class="text-blue-200">Materi Desain</div>
                </div>
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl border border-white/10">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">24/7</div>
                    <div class="text-blue-200">Akses Belajar</div>
                </div>
                <div class="bg-blue-900/50 backdrop-blur-md p-8 rounded-xl border border-white/10">
                    <div class="text-5xl font-bold text-[#FFB302] mb-3">100%</div>
                    <div class="text-blue-200">Online</div>
                </div>
            </div>
            <a href="auth/register.php" 
               class="bg-[#FFB302] text-blue-900 px-8 py-4 rounded-full text-lg font-semibold hover:bg-[#F7CE68] transition transform hover:-translate-y-1 duration-300 inline-block">
                Mulai Perjalanan Desainmu
            </a>
        </div>
    </section>

    <footer class="py-12 bg-blue-900/50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-8 md:mb-0 flex items-center">
                    <i class="fas fa-hexagon text-[#FFB302] text-3xl mr-2"></i>
                    <span class="text-2xl font-bold text-white">DesignHive</span>
                </div>
                <div class="space-x-6">
                    <a href="#" class="text-2xl text-blue-200 hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-2xl text-blue-200 hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-2xl text-blue-200 hover:text-[#FFB302] transition-colors">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="mt-8 text-sm text-blue-200">
                &copy; <?= date('Y') ?> DesignHive - Platform Pembelajaran DKV SMK Negeri 1 Bantul
            </div>
        </div>
    </footer>
</body>
</html>
