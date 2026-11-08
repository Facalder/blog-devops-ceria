# 📋 Blueprint Anggota 4: Secret Security Engineer (Secret Management & Leak Detection)

## 🎯 Identitas Peran
- **Peran**: Secret Management & Access Security Engineer
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-secret-scan.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-secret-scan.yml)
- **Fokus Utama**: Mengelola deteksi kebocoran kredensial rahasia (*Secret Leak Detection* via Trivy), memastikan kebijakan *Zero Hardcoded Credentials*, serta konfigurasi environment secrets.
- **Target Skor Rubrik**: Rubrik 3 Poin 3 (Secret Management) (Bobot 10% - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/devsecops-secret-scan.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-secret-scan.yml)
- [`.env.example`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.env.example)
- [`.gitignore`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.gitignore)
- [`docs/devsecops/devsecops-and-secret-management.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/devsecops-and-secret-management.md)
- [`app/Models/User.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Models/User.php)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Implementasi Pipeline Deteksi Kebocoran Secret (Trivy)**
   - **File**: `.github/workflows/devsecops-secret-scan.yml`
   - **Deskripsi Teknis**: Merancang workflow mandiri menggunakan `aquasecurity/trivy-action` dengan modul `scanners: secret` untuk memindai seluruh file commit dari token/password yang terekspos.
   - **Pesan Commit Contoh**: `sec: establish dedicated devsecops pipeline for automated secret leak scanning`

2. **Perubahan 2: Pengamanan File Kredensial Sensitif & GitHub Secrets Setup**
   - **File**: `.gitignore`, `.env.example`, `docs/devsecops/devsecops-and-secret-management.md`
   - **Deskripsi Teknis**: Menjamin `.env` tidak pernah ter-commit, mendokumentasikan pemetaan variabel `SONAR_TOKEN` pada GitHub Secrets, dan mengonfigurasi proteksi exception production.
   - **Pesan Commit Contoh**: `sec: enforce zero hardcoded credentials and isolation of environment secrets`

3. **Perubahan 3: Mitigasi Elevation of Privilege (RBAC) & Mass Assignment Guard**
   - **File**: `app/Models/User.php`, `tests/Feature/Security/StrideIdeTest.php`
   - **Deskripsi Teknis**: Mengamankan kolom `role` pada model `User` agar kebal dari *mass assignment*, menambahkan middleware proteksi otorisasi admin, dan menulis test case RBAC.
   - **Pesan Commit Contoh**: `sec: harden user role authorization and add rbac security test cases`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan pemindaian secret lokal menggunakan Trivy (jika terinstall) atau memverifikasi file `.env` ter-ignore:
  ```bash
  git check-ignore -v .env
  ```
- Memverifikasi workflow `DevSecOps 4 - Secret Leak Detection` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 6 Poin 6.3**: Menjelaskan kebijakan Zero Hardcoded Credentials, cara setup GitHub Secrets, dan hasil scan Trivy Secret.
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Mendemonstrasikan bahwa `.env` diabaikan oleh Git dan menunjukkan bukti eksekusi workflow `secret-scan` di GitHub Actions.
