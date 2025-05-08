# Diagram Alur Sistem Pembelajaran Desain Grafis Interaktif

## 1. Alur Utama Sistem
```mermaid
graph TD
    A[Mulai] --> B[Landing Page]
    B --> C{Sudah Login?}
    C -->|Tidak| D[Login/Register]
    C -->|Ya| E{Role User}
    
    D --> |Login Berhasil| E
    E -->|Admin| F[Dashboard Admin]
    E -->|Siswa| G[Dashboard Siswa]
    
    F --> H[Manajemen User]
    F --> I[Manajemen Konten]
    F --> J[Laporan & Analitik]
    
    G --> K[Akses Materi]
    G --> L[Kuis & Ujian]
    G --> M[Profil & Progress]
```

## 2. Alur Pembelajaran Siswa
```mermaid
graph TD
    A[Dashboard Siswa] --> B[Pilih Bab]
    B --> C[Pelajari Materi]
    C --> D[Kuis Bab]
    D --> E{Lulus Kuis?}
    E -->|Tidak| C
    E -->|Ya| F{Semua Bab Selesai?}
    F -->|Tidak| B
    F -->|Ya| G[Ujian Akhir]
    G --> H{Lulus Ujian?}
    H -->|Tidak| I[Review Materi]
    I --> G
    H -->|Ya| J[Dapat Sertifikat]
```

## 3. Alur Manajemen Konten Admin
```mermaid
graph TD
    A[Dashboard Admin] --> B[Manajemen Bab]
    B --> C[Tambah/Edit Bab]
    B --> D[Tambah/Edit Sub-Bab]
    B --> E[Upload Materi]
    
    A --> F[Manajemen Kuis]
    F --> G[Buat Soal]
    F --> H[Atur Jawaban]
    F --> I[Set Kriteria Kelulusan]
    
    A --> J[Monitor Progress]
    J --> K[Lihat Statistik]
    J --> L[Generate Laporan]
```

## 4. Alur Sistem Penilaian
```mermaid
graph TD
    A[Mulai Kuis] --> B[Timer Berjalan]
    B --> C[Jawab Soal]
    C --> D{Waktu Habis?}
    D -->|Tidak| C
    D -->|Ya| E[Submit Jawaban]
    E --> F[Penilaian Otomatis]
    F --> G{Lulus?}
    G -->|Ya| H[Update Progress]
    G -->|Tidak| I[Tampilkan Feedback]
    H --> J[Tambah Poin & XP]
    J --> K{Level Up?}
    K -->|Ya| L[Update Level & Badge]
```

## 5. Alur Interaksi Sistem
```mermaid
graph TD
    A[User Request] --> B[Auth Check]
    B --> C{Valid Session?}
    C -->|Tidak| D[Redirect ke Login]
    C -->|Ya| E[Check Permission]
    
    E --> F{Authorized?}
    F -->|Tidak| G[Access Denied]
    F -->|Ya| H[Process Request]
    
    H --> I[Database Query]
    I --> J[Generate Response]
    J --> K[Return Result]
    
    K --> L{Error?}
    L -->|Ya| M[Show Error Message]
    L -->|Tidak| N[Show Success]
```

## 6. Alur Sertifikasi
```mermaid
graph TD
    A[Selesai Semua Bab] --> B{Nilai >= 70?}
    B -->|Tidak| C[Review Materi]
    B -->|Ya| D[Generate Sertifikat]
    
    D --> E[Simpan ke Database]
    E --> F[Kirim Notifikasi]
    F --> G[Download Sertifikat]
    
    C --> H[Ujian Ulang]
    H --> B
```

## 7. Alur Gamifikasi
```mermaid
graph TD
    A[Aktivitas User] --> B{Jenis Aktivitas}
    B -->|Selesai Kuis| C[+10 Poin]
    B -->|Selesai Bab| D[+50 Poin]
    B -->|Komentar| E[+2 Poin]
    
    C --> F[Update Total Poin]
    D --> F
    E --> F
    
    F --> G{Check Level Up}
    G -->|Ya| H[Update Level]
    H --> I[Check Badge]
    I --> J[Award Badge]
```

Catatan: Diagram-diagram ini menggambarkan alur utama sistem. Setiap alur dapat memiliki sub-proses dan penanganan error yang lebih detail dalam implementasinya.
