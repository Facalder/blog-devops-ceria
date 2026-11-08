# 📋 Blueprint Anggota 2: Backend Security Engineer (Composer Audit)

## 🎯 Identitas Peran
- **Peran**: Software Supply Chain Security Engineer (Backend PHP)
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-composer-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-composer-audit.yml)
- **Fokus Utama**: Mengelola audit celah keamanan dependensi backend (PHP & Composer), memeriksa advisories kerentanan paket pihak ketiga, dan menindaklanjuti patch versi paket rentan.
- **Target Skor Rubrik**: Rubrik 3 Poin 2 (Dependency Check di Pipeline - PHP: composer audit) (Bobot 10% - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/devsecops-composer-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-composer-audit.yml)
- [`docs/devsecops/devsecops-and-secret-management.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/devsecops-and-secret-management.md)
- [`composer.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/composer.json)
- [`composer.lock`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/composer.lock)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Implementasi Pipeline Audit Kerentanan Dependensi PHP**
   - **File**: `.github/workflows/devsecops-composer-audit.yml`
   - **Deskripsi Teknis**: Merancang workflow mandiri untuk audit otomatis pustaka PHP menggunakan `composer audit --locked` yang terhubung dengan Packagist Security Advisories Database.
   - **Pesan Commit Contoh**: `sec: establish dedicated devsecops pipeline for php composer dependency audit`

2. **Perubahan 2: Evaluasi & Mitigasi Dependensi Berpotensi Celah**
   - **File**: `composer.json`, `composer.lock`
   - **Deskripsi Teknis**: Melakukan audit lokal dan memastikan seluruh 100+ paket pihak ketiga berada pada versi patch yang aman dari celah CVE.
   - **Pesan Commit Contoh**: `sec: audit locked composer dependencies and verify zero security vulnerabilities`

3. **Perubahan 3: Dokumentasi Analisis Dependensi Backend pada Laporan**
   - **File**: `docs/devsecops/devsecops-and-secret-management.md`
   - **Deskripsi Teknis**: Menyusun panduan evaluasi hasil scan `composer audit` dan prosedur mitigasi penanganan CVE bagi tim pengembang.
   - **Pesan Commit Contoh**: `docs: document backend dependency audit procedures and vulnerability remediation`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan audit keamanan lokal di terminal:
  ```bash
  composer audit --locked
  ```
- Memverifikasi workflow `DevSecOps 2 - Backend PHP Dependency Audit` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 6 Poin 6.2**: Menjelaskan proses audit dependensi PHP, hasil `composer audit` (0 vulnerabilities), dan database advisory yang digunakan.
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Mendemonstrasikan eksekusi `composer audit --locked` di terminal dan menunjukkan hasil job passing di GitHub Actions.
