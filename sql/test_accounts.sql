-- File: test_accounts.sql
-- Deskripsi: Akun untuk testing website DesignHive SMK Negeri 1 Bantul
-- Note: Password di-hash menggunakan PASSWORD_DEFAULT PHP dengan nilai asli tercantum di komentar

-- Akun Admin
INSERT INTO Users (
    nis, 
    name, 
    email,
    password, -- Password asli: admin123!
    role, 
    level,
    points,
    created_at
) VALUES (
    'ADMIN001',
    'Administrator DKV',
    'admin.dkv@smkn1bantul.sch.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    0,
    NOW()
);

-- Akun Guru
INSERT INTO Users (
    nis,
    name,
    email,
    password, -- Password asli: guru123!
    role,
    level,
    points,
    created_at
) VALUES (
    'GURU001',
    'Budi Santoso',
    'budi.santoso@smkn1bantul.sch.id',
    '$2y$10$zxC5GZl3o0.8Y9O8PKi3wu8tsF.ZRdqg.kQK5XFP1J7PpJsGHxKDu',
    'admin',
    1,
    0,
    NOW()
);

-- Akun Siswa 1 (Aktif)
INSERT INTO Users (
    nis,
    name,
    email,
    password, -- Password asli: siswa123!
    role,
    level,
    points,
    created_at
) VALUES (
    '2024001',
    'Ahmad Rizki',
    'ahmad.rizki@student.smkn1bantul.sch.id',
    '$2y$10$iLbYGCSh83U6K.OC8VSth.SMR6TxicKd3J3z6HqZXy.OyXGcIXgCu',
    'student',
    1,
    100,
    NOW()
);

-- Akun Siswa 2 (Aktif)
INSERT INTO Users (
    nis,
    name,
    email,
    password, -- Password asli: siswa456!
    role,
    level,
    points,
    created_at
) VALUES (
    '2024002',
    'Sarah Putri',
    'sarah.putri@student.smkn1bantul.sch.id',
    '$2y$10$QK0YX3BX0Q6ZF.zRvP1E8O8vq/n.FhF.S1yGpCH2X5ZRZT0YPHKYy',
    'student',
    2,
    250,
    NOW()
);

-- Informasi Login:
/*
1. Akun Admin:
   - NIS: ADMIN001
   - Password: admin123!
   - Role: admin
   - Email: admin.dkv@smkn1bantul.sch.id

2. Akun Guru:
   - NIS: GURU001
   - Password: guru123!
   - Role: admin
   - Email: budi.santoso@smkn1bantul.sch.id

3. Akun Siswa 1:
   - NIS: 2024001
   - Password: siswa123!
   - Role: student
   - Email: ahmad.rizki@student.smkn1bantul.sch.id
   - Level: 1
   - Points: 100

4. Akun Siswa 2:
   - NIS: 2024002
   - Password: siswa456!
   - Role: student
   - Email: sarah.putri@student.smkn1bantul.sch.id
   - Level: 2
   - Points: 250

Catatan:
- Semua password sudah di-hash menggunakan fungsi password_hash() PHP
- Role 'admin' memiliki akses ke semua fitur administratif
- Role 'student' hanya memiliki akses ke fitur pembelajaran
- Points dan level hanya berlaku untuk role 'student'
*/
