# 📋 Blueprint Anggota 5: SAST & Feature Developer 1 (Comments & Vuln Scan)

## 🎯 Identitas Peran
- **Peran**: SAST Vulnerability Engineer & Application Developer (Interaksi)
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-05-sast-vulnerability.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-05-sast-vulnerability.yml)
- **Fokus Utama**: Mengelola Static Application Security Testing (SAST) & pemindaian celah CVE filesystem via Trivy, serta mengembangkan fitur interaksi komentar blog ([`Comment`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Models/Comment.php)) yang aman dari XSS.
- **Target Skor Rubrik**: Rubrik 3 Poin 1 & 2 (SAST / Vulnerability Scan) & Rubrik 2 (Perubahan Aplikasi & Test Cases 10% - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/devsecops-05-sast-vulnerability.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-05-sast-vulnerability.yml)
- [`app/Models/Comment.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Models/Comment.php)
- [`database/migrations/2026_11_07_123422_create_comments_table.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/database/migrations/2026_11_07_123422_create_comments_table.php)
- [`database/factories/CommentFactory.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/database/factories/CommentFactory.php)
- [`tests/Feature/Blog/PostCommentsTest.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Feature/Blog/PostCommentsTest.php)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Implementasi Pipeline Pemindaian Kerentanan Filesystem & SAST (Trivy)**
   - **File**: `.github/workflows/devsecops-05-sast-vulnerability.yml`
   - **Deskripsi Teknis**: Merancang workflow mandiri menggunakan Trivy (`scanners: vuln`) untuk mendeteksi kerentanan CVE tingkat keparahan CRITICAL dan HIGH pada seluruh filesystem repositori.
   - **Pesan Commit Contoh**: `sec: establish dedicated devsecops pipeline for trivy filesystem vulnerability scanning`

2. **Perubahan 2: Skema Relasi Database & Model Comment Aman dari Mass Assignment**
   - **File**: `database/migrations/*_create_comments_table.php`, `app/Models/Comment.php`
   - **Deskripsi Teknis**: Menyiapkan relasi tabel Post dan Comments, proteksi kolom `$fillable`, dan factory testing database.
   - **Pesan Commit Contoh**: `feat(comment): build comment model schema relationships and factory`

3. **Perubahan 3: Pembuatan Test Suite Fitur Komentar & Sanitasi XSS**
   - **File**: `tests/Feature/Blog/PostCommentsTest.php`
   - **Deskripsi Teknis**: Menulis minimal 3 test case Pest untuk memvalidasi pembuatan komentar oleh user terautentikasi, penolakan input kosong, dan otorisasi hapus komentar.
   - **Pesan Commit Contoh**: `test(comment): add comprehensive pest test suite for post comments feature`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan test case komentar secara lokal:
  ```bash
  vendor/bin/pest tests/Feature/Blog/PostCommentsTest.php
  ```
- Memverifikasi workflow `DevSecOps 5 - SAST & Vulnerability Scan` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 3 & Bab 6**: Mendokumentasikan fitur komentar dan hasil pemindaian celah filesystem Trivy.
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Mendemonstrasikan fitur pengiriman komentar di browser dan menunjukkan hasil scan Trivy yang bebas dari kerentanan kritis.
