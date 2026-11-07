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

## 🔍 2. Konfigurasi & Integrasi SonarQube

Analisis kualitas kode statis dikendalikan oleh file konfigurasi [`sonar-project.properties`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/sonar-project.properties) yang terletak di root repositori.

### A. Rincian Konfigurasi `sonar-project.properties`:
```properties
# Identifikasi Project
sonar.projectKey=blog-devops-ceria
sonar.projectName=Blog DevOps Ceria
sonar.projectVersion=1.0.0

# Direktori Sumber & Test
sonar.sources=app,routes,config,resources/views
sonar.tests=tests
sonar.sourceEncoding=UTF-8

# Filter Inclusions & Exclusions
sonar.test.inclusions=tests/**/*Test.php,tests/**/*.php
sonar.exclusions=vendor/**,node_modules/**,storage/**,public/**,database/seeders/**,database/factories/**,bootstrap/cache/**

# Integrasi Code Coverage dari Pest
sonar.php.coverage.reportPaths=coverage.xml
```

### B. Metrik Kualitas Kode yang Dipantau:
1. **Bugs**: Kesalahan kode yang berpotensi menimbulkan *runtime exception* atau kegagalan fungsional.
2. **Vulnerabilities**: Celah keamanan yang berpotensi dieksploitasi oleh pihak luar (misal: SQL Injection, XSS).
3. **Security Hotspots**: Bagian kode sensitif yang membutuhkan peninjauan manual (misal: konfigurasi enkripsi atau hashing).
4. **Code Smells**: Masalah kemudahan pemeliharaan (*maintainability*), duplikasi kode, atau kompleksitas siklomatis yang tinggi.
5. **Code Coverage**: Persentase baris kode aplikasi yang telah tervalidasi oleh *test cases*.

### C. Integrasi GitHub Actions ke SonarCloud / SonarQube
1. Buat project baru di [SonarCloud.io](https://sonarcloud.io) atau SonarQube server tim Anda.
2. Generate Token akses di SonarQube (`My Account -> Security -> Generate Tokens`).
3. Tambahkan token tersebut ke **GitHub Secrets** repositori:
   - Key: `SONAR_TOKEN`
   - Key: `SONAR_HOST_URL` (Opsional untuk SonarCloud, wajib jika menggunakan self-hosted SonarQube instance).
4. Pada setiap push/PR, job `sonarqube` pada pipeline GitHub Actions akan secara otomatis menjalankan pemindaian dan memperbarui status *Quality Gate*.
