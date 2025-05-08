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
</head>
<body class="professional-pattern min-h-screen font-[Poppins]">
    <!-- Navigation -->
    <nav class="nav-professional fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-20">
                <div class="flex items-center space-x-3">
                    <div class="logo-container w-12 h-12">
                        <div class="logo-hex"></div>
                        <div class="logo-inner">
                            <span>@</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl font-bold text-white">DesignHive</span>
                        <span class="text-sm text-blue-200 ml-2">SMK Negeri 1 Bantul</span>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="auth/login.php" class="text-white hover:text-[#FFB302] transition-colors flex items-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span>Masuk</span>
                    </a>
                    <a href="auth/register.php" 
                       class="btn-primary hover:shadow-[0_0_20px_rgba(255,179,2,0.3)] transform hover:-translate-y-1 transition duration-300">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="min-h-screen flex items-center justify-center text-center px-4 relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-secondary opacity-50"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M30 0L60 30L30 60L0 30L30 0Z\" fill=\"%23FFB302\" fill-opacity=\"0.05\"/%3E%3C/svg%3E')] bg-repeat"></div>
        </div>
        
        <div class="max-w-4xl z-10 animate-float">
            <div class="logo-container w-24 h-24 mb-8 mx-auto">
                <div class="logo-hex"></div>
                <div class="logo-inner">
                    <span>@</span>
                </div>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 text-white">
                Selamat Datang di 
                <span class="text-gradient">DesignHive</span>
            </h1>
            <p class="text-xl md:text-2xl mb-12 text-blue-200">
                Platform pembelajaran digital untuk siswa DKV SMK Negeri 1 Bantul.
                <span class="block mt-2">Belajar desain grafis kapan saja, di mana saja.</span>
            </p>
            <button onclick="window.location.href='auth/register.php'" 
                    class="btn-primary text-lg px-10 py-5 hover:shadow-[0_0_30px_rgba(255,179,2,0.3)] transform hover:-translate-y-1 transition duration-300">
                <i class="fas fa-graduation-cap mr-2"></i>
                Mulai Belajar Sekarang
            </button>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-20 px-4 relative">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16 text-white">
                Fitur <span class="text-gradient">Unggulan</span>
            </h2>
            <div class="card-grid">
                <div class="card-glass p-8 rounded-xl text-center hover-lift">
                    <div class="icon-container mx-auto mb-6">
                        <i class="fas fa-book-open text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-white">Materi Terstruktur</h3>
                    <p class="text-blue-200">Kurikulum yang dirancang khusus sesuai kebutuhan industri DKV</p>
                </div>
                <div class="card-glass p-8 rounded-xl text-center hover-lift">
                    <div class="icon-container mx-auto mb-6">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-white">Latihan Interaktif</h3>
                    <p class="text-blue-200">Praktik langsung dengan tools desain dan feedback instant</p>
                </div>
                <div class="card-glass p-8 rounded-xl text-center hover-lift">
                    <div class="icon-container mx-auto mb-6">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-white">Forum Diskusi</h3>
                    <p class="text-blue-200">Diskusi dengan guru dan sesama siswa DKV</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 px-4 relative">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-center mb-16 text-white">
                Bergabung dengan <span class="text-gradient">DesignHive</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
                <div class="stat-card">
                    <div class="text-5xl font-bold text-gradient mb-3">1000+</div>
                    <div class="text-blue-200">Siswa Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="text-5xl font-bold text-gradient mb-3">50+</div>
                    <div class="text-blue-200">Materi Desain</div>
                </div>
                <div class="stat-card">
                    <div class="text-5xl font-bold text-gradient mb-3">24/7</div>
                    <div class="text-blue-200">Akses Belajar</div>
                </div>
                <div class="stat-card">
                    <div class="text-5xl font-bold text-gradient mb-3">100%</div>
                    <div class="text-blue-200">Online</div>
                </div>
            </div>
            <div class="text-center">
                <a href="auth/register.php" 
                   class="btn-primary text-lg px-10 py-5 hover:shadow-[0_0_30px_rgba(255,179,2,0.3)] transform hover:-translate-y-1 transition duration-300 inline-block">
                    <i class="fas fa-rocket mr-2"></i>
                    Mulai Perjalanan Desainmu
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 relative">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div class="flex items-center space-x-3 mb-8 md:mb-0">
                    <div class="logo-container w-12 h-12">
                        <div class="logo-hex"></div>
                        <div class="logo-inner">
                            <span>@</span>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-white">DesignHive</span>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="icon-container w-12 h-12">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="icon-container w-12 h-12">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="icon-container w-12 h-12">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="divider my-8"></div>
            <div class="text-center text-sm text-blue-200">
                &copy; <?= date('Y') ?> DesignHive - Platform Pembelajaran DKV SMK Negeri 1 Bantul
            </div>
        </div>
    </footer>
</body>
</html>
