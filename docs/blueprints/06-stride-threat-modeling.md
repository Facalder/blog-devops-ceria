# 📋 Blueprint Anggota 6: STRIDE Security & Feature Developer (Threat Modeling & Search)

## 🎯 Identitas Peran
- **Peran**: STRIDE Threat Modeling & Application Hardening Engineer
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-stride-tests.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-stride-tests.yml)
- **Fokus Utama**: Mengelola workflow verifikasi kepatuhan keamanan STRIDE (*Security Tests* di CI), memvalidasi mitigasi 6 kategori STRIDE melalui automated test suite, mitigasi DoS pada pencarian, dan mengembangkan fitur filter kategori artikel.
- **Target Skor Rubrik**: Rubrik 3 Poin 1 (Analisis STRIDE) & Rubrik 2 (Perubahan Aplikasi & Test Cases 10% - Sangat Baik).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/devsecops-stride-tests.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-stride-tests.yml)
- [`tests/Feature/Security/StrideThreatModelingTest.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Feature/Security/StrideThreatModelingTest.php)
- [`app/Models/Post.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/app/Models/Post.php)
- [`resources/views/welcome.blade.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/resources/views/welcome.blade.php)
- [`tests/Feature/Blog/PostSearchFilterTest.php`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Feature/Blog/PostSearchFilterTest.php)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Implementasi Pipeline & Test Suite Otomasi STRIDE (6 Kategori)**
   - **File**: `.github/workflows/devsecops-stride-tests.yml`, `tests/Feature/Security/StrideThreatModelingTest.php`
   - **Deskripsi Teknis**: Merancang workflow mandiri dan test suite Pest untuk memverifikasi 6 kategori ancaman STRIDE (Spoofing, Tampering, Repudiation, Information Disclosure, Denial of Service, Elevation of Privilege) secara otomatis di CI.
   - **Pesan Commit Contoh**: `test(stride): implement automated threat modeling security test suite for all 6 categories`

2. **Perubahan 2: Query Scopes Pencarian & Filter Kategori Efisien (Anti-DoS)**
   - **File**: `app/Models/Post.php`, `resources/views/welcome.blade.php`
   - **Deskripsi Teknis**: Menambahkan method `scopeSearch` dan `scopeByCategory` dengan penanganan pembatasan pagination ketat agar mencegah request berlebih (*Denial of Service*).
   - **Pesan Commit Contoh**: `feat(search): implement search and category filter query scopes with strict pagination`

3. **Perubahan 3: Pembuatan Test Suite Pencarian & Isolasi Artikel Draft**
   - **File**: `tests/Feature/Blog/PostSearchFilterTest.php`
   - **Deskripsi Teknis**: Menulis minimal 3 test case Pest untuk memvalidasi filter kategori, pencarian kata kunci, dan memastikan artikel berstatus `draft` tidak terekspos ke publik (*Information Disclosure*).
   - **Pesan Commit Contoh**: `test(search): add pest test suite for article search filter and draft isolation`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan test case pencarian secara lokal:
  ```bash
  vendor/bin/pest tests/Feature/Blog/PostSearchFilterTest.php
  ```
- Memverifikasi workflow `DevSecOps 6 - STRIDE Security Compliance` berjalan otomatis dan berstatus passing pada Pull Request.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 3, Bab 4, & Bab 6**: Mendokumentasikan fitur pencarian artikel dan alur eksekusi pipeline pengujian keamanan STRIDE.
- **Video Presentasi (Durasi 1.5 - 2 Menit)**: Mendemonstrasikan live search di beranda dan pembuktian bahwa postingan draft tidak dapat diakses publik.
