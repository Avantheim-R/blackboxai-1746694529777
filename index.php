<?php
session_start();

// If user is logged in, redirect to appropriate dashboard
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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            color: #f9fafb;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .hero {
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.pexels.com/photos/7439135/pexels-photo-7439135.jpeg') center/cover;
            opacity: 0.2;
            z-index: -1;
        }
        .btn-primary {
            background: linear-gradient(90deg, #FF6B6B 0%, #4ECDC4 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 1.125rem;
            box-shadow: 0 8px 15px rgba(255, 107, 107, 0.4);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(255, 107, 107, 0.6);
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .school-info {
            background: rgba(255, 255, 255, 0.95);
            color: #1a202c;
            border-radius: 20px;
            padding: 2rem;
            margin: 4rem auto;
            max-width: 800px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <nav class="fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold">
                        <i class="fas fa-hexagon text-white mr-2"></i>
                        DesignHive
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="auth/login.php" class="text-white hover:text-gray-200 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </a>
                    <a href="auth/register.php" 
                       class="bg-white text-gray-800 px-4 py-2 rounded-full hover:bg-gray-100 transition">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero min-h-screen flex items-center justify-center text-center px-4">
        <div class="max-w-4xl">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-float">
                Selamat Datang di DesignHive
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-100">
                Platform pembelajaran digital untuk siswa DKV SMK Negeri 1 Bantul.
                Belajar desain grafis kapan saja, di mana saja.
            </p>
            <button onclick="window.location.href='auth/register.php'" class="btn-primary">
                Mulai Belajar
            </button>
        </div>
    </section>

    <section class="school-info text-center">
        <img src="https://smkn1bantul.sch.id/wp-content/uploads/2020/05/logo-smk.png" 
             alt="Logo SMK Negeri 1 Bantul" 
             class="h-24 mx-auto mb-6">
        <h2 class="text-3xl font-bold mb-4">SMK Negeri 1 Bantul</h2>
        <p class="text-gray-600 mb-6">
            Program Keahlian Desain Komunikasi Visual
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
            <div class="text-center">
                <i class="fas fa-graduation-cap text-4xl text-gray-700 mb-4"></i>
                <h3 class="font-semibold mb-2">Pendidikan Berkualitas</h3>
                <p class="text-gray-600">Pembelajaran terstruktur dengan standar industri</p>
            </div>
            <div class="text-center">
                <i class="fas fa-laptop-code text-4xl text-gray-700 mb-4"></i>
                <h3 class="font-semibold mb-2">Belajar Digital</h3>
                <p class="text-gray-600">Akses materi kapan saja dan di mana saja</p>
            </div>
            <div class="text-center">
                <i class="fas fa-certificate text-4xl text-gray-700 mb-4"></i>
                <h3 class="font-semibold mb-2">Sertifikasi</h3>
                <p class="text-gray-600">Dapatkan sertifikat setelah menyelesaikan kursus</p>
            </div>
        </div>
    </section>

    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">Fitur Pembelajaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card rounded-lg p-6 text-center">
                    <i class="fas fa-book-open text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Materi Terstruktur</h3>
                    <p>Kurikulum yang dirancang khusus sesuai kebutuhan industri DKV</p>
                </div>
                <div class="feature-card rounded-lg p-6 text-center">
                    <i class="fas fa-tasks text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Latihan Interaktif</h3>
                    <p>Praktik langsung dengan tools desain dan feedback instant</p>
                </div>
                <div class="feature-card rounded-lg p-6 text-center">
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Forum Diskusi</h3>
                    <p>Diskusi dengan guru dan sesama siswa DKV</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <img src="https://smkn1bantul.sch.id/wp-content/uploads/2020/05/logo-smk.png" 
                         alt="Logo SMK" 
                         class="h-12 inline-block">
                    <span class="ml-4">SMK Negeri 1 Bantul</span>
                </div>
                <div class="space-x-4">
                    <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="mt-4 text-sm">
                &copy; <?= date('Y') ?> DesignHive - Platform Pembelajaran DKV SMK Negeri 1 Bantul
            </div>
        </div>
    </footer>
</body>
</html>
