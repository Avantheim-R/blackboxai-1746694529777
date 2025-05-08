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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembelajaran Desain Grafis Interaktif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-pattern {
            background-color: #4f46e5;
            background-image: radial-gradient(at 0% 0%, rgb(134, 239, 172) 0, transparent 50%),
                            radial-gradient(at 100% 0%, rgb(192, 132, 252) 0, transparent 50%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">DesignEdu</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="auth/login.php" class="text-gray-700 hover:text-indigo-600">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </a>
                    <a href="auth/register.php" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-300">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-pattern min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">
                        Belajar Desain Grafis Secara Interaktif
                    </h1>
                    <p class="text-xl mb-8 text-gray-100">
                        Tingkatkan kemampuan desain grafis Anda dengan pembelajaran interaktif, 
                        latihan praktis, dan bimbingan langsung dari para ahli.
                    </p>
                    <div class="space-x-4">
                        <a href="auth/register.php" 
                           class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                            Mulai Belajar
                        </a>
                        <a href="#features" 
                           class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition duration-300">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <img src="https://images.pexels.com/photos/7439135/pexels-photo-7439135.jpeg" 
                         alt="Design Learning" 
                         class="rounded-lg shadow-2xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Sistem pembelajaran yang dirancang untuk memaksimalkan pengalaman belajar Anda
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 border rounded-lg hover:shadow-lg transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <i class="fas fa-laptop-code text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Pembelajaran Interaktif</h3>
                    <p class="text-gray-600">
                        Materi pembelajaran yang interaktif dengan latihan praktis dan umpan balik langsung
                    </p>
                </div>

                <div class="p-6 border rounded-lg hover:shadow-lg transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <i class="fas fa-trophy text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Sistem Gamifikasi</h3>
                    <p class="text-gray-600">
                        Dapatkan poin, lencana, dan sertifikat sebagai penghargaan atas pencapaian Anda
                    </p>
                </div>

                <div class="p-6 border rounded-lg hover:shadow-lg transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <i class="fas fa-comments text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Forum Diskusi</h3>
                    <p class="text-gray-600">
                        Berinteraksi dengan sesama peserta dan mentor dalam forum diskusi
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Testimoni Peserta</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Apa kata mereka yang telah mengikuti pembelajaran di platform kami
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "Pembelajaran yang sangat interaktif dan menyenangkan. Saya bisa belajar 
                        desain grafis dengan lebih mudah."
                    </p>
                    <div class="font-semibold">Ahmad S.</div>
                    <div class="text-gray-500 text-sm">Mahasiswa</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "Sistem gamifikasi membuat saya lebih termotivasi untuk terus belajar 
                        dan mengembangkan keterampilan."
                    </p>
                    <div class="font-semibold">Sarah R.</div>
                    <div class="text-gray-500 text-sm">Freelancer</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "Materi yang komprehensif dan forum diskusi yang aktif membuat 
                        proses belajar menjadi lebih efektif."
                    </p>
                    <div class="font-semibold">Budi W.</div>
                    <div class="text-gray-500 text-sm">Karyawan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-indigo-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-8">
                Siap Untuk Memulai Perjalanan Desain Anda?
            </h2>
            <a href="auth/register.php" 
               class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">DesignEdu</h3>
                    <p class="text-gray-400">
                        Platform pembelajaran desain grafis interaktif untuk semua kalangan
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Tautan</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white">Fitur</a></li>
                        <li><a href="auth/login.php" class="hover:text-white">Masuk</a></li>
                        <li><a href="auth/register.php" class="hover:text-white">Daftar</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li><i class="fas fa-envelope mr-2"></i> info@designedu.com</li>
                        <li><i class="fas fa-phone mr-2"></i> +62 123 4567 890</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Sosial Media</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-2xl hover:text-white">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-2xl hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-2xl hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-2xl hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p>&copy; <?= date('Y') ?> DesignEdu. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
