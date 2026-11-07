# Panduan Integrasi SonarQube & Strategi Testing

Dokumen ini menjelaskan strategi pengujian otomatis (*automated testing*) dan analisis kualitas kode (*code quality analysis*) menggunakan **Pest 5 / PHPUnit** dan **SonarQube / SonarCloud**.

---

## 🧪 1. Strategi Automated Testing (Pest 5)

Aplikasi Blog DevOps Ceria menggunakan framework pengujian **Pest 5** yang berjalan di atas PHPUnit, dengan isolasi basis data SQLite *in-memory* (`:memory:`).

### A. Struktur Direktori Pengujian
- [`tests/Unit/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Unit): Pengujian logika fungsi dan model independen.
- [`tests/Feature/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/tests/Feature): Pengujian alur integrasi HTTP, otorisasi Fortify, dan komponen Livewire:
  - `Admin/CategoriesManagementTest.php`: CRUD Kategori artikel.
  - `Admin/PostsManagementTest.php`: CRUD Artikel blog & publish flow.
  - `Admin/UsersManagementTest.php`: Manajemen pengguna & role.
  - `Auth/AuthenticationTest.php`: Login, Logout, Session.
  - `Auth/RegistrationTest.php`: Registrasi akun baru.
  - `BlogPublicTest.php`: Akses publik membaca artikel.

### B. Menjalankan Test Suite
```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan test secara spesifik berdasarkan file
php artisan test tests/Feature/BlogPublicTest.php

# Menjalankan test dengan filter method
php artisan test --filter=test_user_can_view_post
```

### C. Menghasilkan Laporan Code Coverage
Untuk mencapai target **coverage > 50%** (Rubrik 1 & 4), pipeline CI menggunakan ekstensi `pcov` untuk menghasilkan laporan dalam format Clover XML:
```bash
vendor/bin/pest --coverage --coverage-clover=coverage.xml
```
File `coverage.xml` selanjutnya diteruskan ke SonarQube untuk dipetakan ke baris kode aktual.

---

## 🔍 2. Konfigurasi & Integrasi SonarCloud (Security & Code Quality)

Analisis kualitas kode statis dan pemindaian kerentanan dikendalikan oleh file konfigurasi [`sonar-project.properties`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/sonar-project.properties) yang terletak di root repositori.

### A. Rincian Konfigurasi `sonar-project.properties`:
```properties
sonar.organization=devops-ceria
sonar.projectKey=devops-ceria_blog-devops-ceria
sonar.projectName=Blog Devops Ceria
sonar.projectVersion=1.0.0

sonar.sources=app,routes,config,resources/views,resources/js
sonar.tests=tests
sonar.sourceEncoding=UTF-8

sonar.test.inclusions=tests/**/*Test.php,tests/**/*.php
sonar.exclusions=vendor/**,node_modules/**,storage/**,public/**,database/seeders/**,database/factories/**,bootstrap/cache/**
sonar.coverage.exclusions=database/**,routes/**,config/**,resources/**
sonar.cpd.exclusions=tests/**

sonar.scm.provider=git

sonar.php.coverage.reportPaths=coverage.xml
sonar.php.tests.reportPath=test-report.xml
sonar.php.phpstan.reportPaths=phpstan-report.json

sonar.qualitygate.wait=false
```

### B. Fitur Unggulan Keamanan & Analisis yang Dimaksimalkan:
1. **Multi-layer SAST & Security Scanning**: Memindai seluruh komponen backend (`app/`, `routes/`, `config/`), template (`resources/views/`), dan frontend JavaScript (`resources/js/`) untuk mendeteksi celah OWASP Top 10 seperti SQL Injection, Stored/Reflected XSS, CSRF, insecure session, dan hardcoded secrets.
2. **Integrasi Eksternal PHPStan (Larastan)**: Laporan statis dan deteksi tipe ketat dari PHPStan diekspor dalam format JSON (`phpstan-report.json`) dan diimpor langsung ke SonarCloud via `sonar.php.phpstan.reportPaths`.
3. **Pelaporan Eksekusi Test (JUnit XML)**: Eksekusi Pest menghasilkan laporan JUnit XML (`test-report.xml`) yang menyajikan metrik jumlah test lulus, durasi eksekusi, dan stabilitas test suite di dashboard SonarCloud.
4. **Isolasi Metrik Code Coverage**: Menerapkan `sonar.coverage.exclusions` pada konfigurasi dan tampilan agar persentase *code coverage* merefleksikan logika bisnis murni di `app/` (> 50%).
5. **Git SCM Provider Tracking**: Melacak riwayat blame Git secara presisi untuk memetakan kepemilikan kode dan evaluasi Quality Gate pada *New Code*.

### C. Metrik Kualitas Kode yang Dipantau:
1. **Bugs**: Kesalahan kode yang berpotensi menimbulkan *runtime exception* atau kegagalan fungsional.
2. **Vulnerabilities**: Celah keamanan yang berpotensi dieksploitasi oleh pihak luar (misal: SQL Injection, XSS).
3. **Security Hotspots**: Bagian kode sensitif yang membutuhkan peninjauan manual (misal: konfigurasi enkripsi atau hashing).
4. **Code Smells**: Masalah kemudahan pemeliharaan (*maintainability*), duplikasi kode, atau kompleksitas siklomatis yang tinggi.
5. **Code Coverage**: Persentase baris kode aplikasi yang telah tervalidasi oleh *test cases*.

### D. Integrasi GitHub Actions ke SonarCloud
1. Konfigurasi terisolasi di workflow [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml).
2. Memanfaatkan secret `SONAR_TOKEN` dari akun SonarCloud.
3. Otomatis berjalan saat push ke branch `main`, `dev`, dan pada Pull Request (PR decoration otomatis).
