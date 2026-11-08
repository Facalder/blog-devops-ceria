# 📋 Blueprint Anggota 3: Frontend Security Engineer (NPM Dependency Audit & Client Security)

## 🎯 Identitas Peran
- **Peran**: Client-Side & Frontend Security Engineer
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-npm-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-npm-audit.yml)
- **Fokus Utama**: Mengelola audit keamanan dependensi frontend (Node.js & NPM), mitigasi ancaman Spoofing dan Tampering sesi, serta sanitasi form antarmuka publik.
- **Target Skor Rubrik**: Rubrik 3 Poin 2 (Dependency Check di Pipeline - Node.js: npm audit) (Bobot 10% - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/devsecops-npm-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-npm-audit.yml)
- [`package.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/package.json)
- [`package-lock.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/package-lock.json)
- [`app/Actions/Fortify/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Actions/Fortify/)
- [`docs/devsecops/threat-modeling-stride.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/threat-modeling-stride.md)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Implementasi Pipeline Audit Kerentanan Dependensi NPM**
   - **File**: `.github/workflows/devsecops-npm-audit.yml`
   - **Deskripsi Teknis**: Merancang workflow mandiri untuk audit otomatis paket JavaScript menggunakan `npm audit --audit-level=high` pada Node.js 22.
   - **Pesan Commit Contoh**: `sec: establish dedicated devsecops pipeline for node npm dependency audit`

2. **Perubahan 2: Mitigasi Spoofing & Tampering pada Sesi Autentikasi**
   - **File**: `app/Actions/Fortify/`, `config/session.php`
   - **Deskripsi Teknis**: Mengonfigurasi rate limiting pada login Fortify, regenerasi ID sesi otomatis, dan pemastian cookie `HttpOnly` serta `SameSite=lax`.
   - **Pesan Commit Contoh**: `sec: harden session cookies and enforce auth rate limiting against spoofing`

3. **Perubahan 3: Pembuatan Test Case Sanitasi Input & Proteksi S-T-R**
   - **File**: `tests/Feature/Security/StrideStrTest.php`
   - **Deskripsi Teknis**: Menulis 3 test case Pest untuk memverifikasi proteksi brute-force login, sanitasi input form artikel, dan event log repudiation.
   - **Pesan Commit Contoh**: `test: add pest security test suite for stride spoofing and tampering mitigations`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan audit keamanan frontend di terminal:
  ```bash
  npm audit --audit-level=high
  ```
- Memverifikasi workflow `DevSecOps 3 - Frontend NPM Dependency Audit` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 6 Poin 6.2**: Menjelaskan proses audit dependensi NPM frontend (`0 vulnerabilities`) dan mitigasi STRIDE kategori S, T, R.
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Mendemonstrasikan hasil scan `npm audit` di terminal dan pembuktian proteksi rate-limiting login.
