# 📋 Blueprint Anggota 5: Code Quality & SonarQube Engineer (Quality Gate & Comments)

## 🎯 Identitas Peran
- **Peran**: Code Quality & SonarQube Engineer
- **Penanggung Jawab Workflow**: [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml)
- **Fokus Utama**: Mengelola analisis kualitas kode terintegrasi SonarCloud / SonarQube (Quality Gate Passed, 0 bugs, 0 vulnerabilities, code smells minimal, dan ingestion coverage), serta mengembangkan fitur interaksi komentar blog yang bersih.
- **Target Skor Rubrik**: Rubrik 1 Poin 4 (SonarQube Terintegrasi - Sangat Baik) & Rubrik 2 Poin 2 (Kualitas Kode Hasil SonarQube - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml)
- [`sonar-project.properties`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/sonar-project.properties)
- [`app/Models/Comment.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Models/Comment.php)
- [`tests/Feature/Blog/PostCommentsTest.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Feature/Blog/PostCommentsTest.php)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Integrasi Pipeline Kualitas Kode SonarCloud & Quality Gate Evaluator**
   - **File**: `.github/workflows/sonarcloud.yml`, `sonar-project.properties`
   - **Deskripsi Teknis**: Mengonfigurasi workflow analisis statis otomatis SonarCloud dengan pembacaan laporan PCOV `coverage.xml`, pemindaian bugs, vulnerabilities, dan pemenuhan Quality Gate PASSED.
   - **Pesan Commit Contoh**: `ci(sonar): optimize automated quality gate workflow and clover coverage ingestion`

2. **Perubahan 2: Skema Relasi Database & Model Comment Aman dari Mass Assignment**
   - **File**: `database/migrations/*_create_comments_table.php`, `app/Models/Comment.php`
   - **Deskripsi Teknis**: Menyiapkan relasi tabel Post dan Comments, proteksi atribut `$fillable`, dan factory testing database.
   - **Pesan Commit Contoh**: `feat(comment): build comment model schema relationships and factory`

3. **Perubahan 3: Pembuatan Test Suite Fitur Komentar & Sanitasi XSS**
   - **File**: `tests/Feature/Blog/PostCommentsTest.php`
   - **Deskripsi Teknis**: Menulis minimal 3 test case Pest untuk memvalidasi pembuatan komentar oleh user terautentikasi, penolakan input kosong, dan otorisasi hapus komentar.
   - **Pesan Commit Contoh**: `test(comment): add comprehensive pest test suite for post comments feature`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan test coverage lokal sebelum pemicuan Sonar:
  ```bash
  vendor/bin/pest --coverage-clover=coverage.xml
  ```
- Memverifikasi workflow `SonarCloud Code Quality Analysis` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 5 (Analisis Kualitas Kode)**: Menjelaskan hasil scan SonarCloud (Bugs: 0, Vulnerabilities: 0, Code Smells minimal, Coverage, Quality Gate: PASSED).
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Menampilkan dashboard SonarCloud/SonarQube dan menjelaskan metrik keandalan kode yang tercapai.
