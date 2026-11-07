# Dokumentasi & Panduan DevOps: Blog DevOps Ceria

Dokumentasi ini disusun untuk memenuhi seluruh kriteria dan rubrik **Ujian Tengah Semester (UTS) Mata Kuliah DevOps (CCK4IBB3)** Program Studi S1 Rekayasa Perangkat Lunak, Telkom University.

---

## 📚 Daftar Isi Dokumentasi

1. [Rencana Strategis & Master Plan CI/CD](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/ci-cd-planning.md)
   - Perencanaan komprehensif CI/CD, DevSecOps, dan Quality Gate sesuai rubrik UTS DevOps.
   - Matriks capaian nilai maksimal (Skor 4), pembagian 3 perubahan fungsional per anggota, dan jadwal fase eksekusi.
   - Aturan operasional ketat (Strict Rules): tata kelola Git Flow, konvensi commit, zero-comment policy, zero hardcoded credentials, dan ambang batas test coverage.
   - Hal-hal kritis yang perlu diperhatikan (Watch-outs & Pitfalls): sinkronisasi lockfile, batasan memori PHPStan/PCOV, penanganan dependency CVE, dan pengarsipan bukti laporan.
   - Definisi kesiapan (Definition of Done) sebelum konfigurasi lanjutan.

2. [Arsitektur CI/CD Pipeline](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/ci-pipeline.md)
   - Penjelasan alur pipeline multi-stage pada GitHub Actions.
   - Konfigurasi job: *Code Quality*, *Frontend Build*, *DevSecOps Dependency Check*, *Testing & Coverage*, dan *SonarQube Analysis*.
   - Diagram alur kerja pipeline.

3. [Threat Modeling STRIDE](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/threat-modeling-stride.md)
   - Analisis keamanan mendalam menggunakan 6 pilar STRIDE (*Spoofing*, *Tampering*, *Repudiation*, *Information Disclosure*, *Denial of Service*, *Elevation of Privilege*).
   - Pemetaan ancaman ke komponen aplikasi, tingkat risiko, dan implementasi mitigasi di Laravel.

4. [DevSecOps & Secret Management](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops-and-secret-management.md)
   - Praktik audit dependensi berkala (`composer audit` dan `npm audit`).
   - Panduan pengelolaan secrets & zero-hardcoded credentials menggunakan GitHub Secrets.
   - Kebijakan *Branch Protection* & *Code Review* pada branch utama (`main`).

5. [SonarQube & Strategi Testing](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/sonarqube-and-testing.md)
   - Konfigurasi `sonar-project.properties` dan integrasi ke SonarQube/SonarCloud.
   - Strategi testing menggunakan Pest 5 & PHPUnit dengan target code coverage > 50% via driver PCOV.

6. [Template Laporan Proyek UTS](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/laporan-uts-template.md)
   - Struktur laporan lengkap mencakup 7 bagian wajib: Deskripsi Aplikasi, CI Pipeline, Perubahan Aplikasi, Testing, Analisis Kualitas Kode, Analisis DevSecOps, dan Retrospektif Tim.

---

## 🛠️ Ringkasan Tech Stack Aplikasi

- **Backend Framework**: Laravel 13 (PHP 8.4)
- **Frontend & Reaktivitas**: Livewire 4, Flux UI 2, Tailwind CSS v4, Vite Plus (`vp`)
- **Autentikasi**: Laravel Fortify
- **Database**: SQLite (`database/database.sqlite`), in-memory SQLite untuk automated testing
- **Testing Framework**: Pest 5 & PHPUnit
- **Static Analysis & Linting**: Larastan / PHPStan (Level 7) & Laravel Pint
- **CI/CD & DevSecOps Platform**: GitHub Actions, SonarQube / SonarCloud, Composer Audit, NPM Audit

---

## 🚀 Panduan Menjalankan Project Secara Lokal

### 1. Prasyarat Sistem
- PHP >= 8.3 / 8.4 dengan ekstensi: `pdo_sqlite`, `mbstring`, `xml`, `intl`, `curl`
- Composer >= 2.x
- Node.js >= 20.x / 22.x & NPM

### 2. Langkah Setup Otomatis
Jalankan perintah berikut untuk menginisialisasi dependensi, file konfigurasi `.env`, app key, migrasi database, dan build frontend:
```bash
composer setup
```

### 3. Menjalankan Server Pengembangan
```bash
composer dev
```
Perintah ini akan menjalankan tiga proses sekaligus secara paralel:
- PHP Development Server (`http://127.0.0.1:8000`)
- Queue Worker
- Vite Plus Development Server

### 4. Menjalankan Quality Gate & Testing
Jalankan validasi CI secara lokal sebelum melakukan push/PR:
```bash
composer ci:check
```
Perintah di atas mengeksekusi:
1. `php artisan config:clear` (Membersihkan konfigurasi cache)
2. `pint --parallel --test` (Pemeriksaan standar coding style)
3. `phpstan analyse --memory-limit=1G` (Analisis tipe statis PHP)
4. `php artisan test` (Eksekusi 40+ unit & feature test suites)

---

## 📊 Matriks Pemetaan Kriteria Penilaian UTS

| Komponen Rubrik | Bobot | Kriteria Kunci | Lokasi Implementasi / Dokumentasi |
|---|:---:|---|---|
| **1. Pipeline CI** | 15% | Multi-stage (Build, Test, Analysis), Best-practice YAML, Test otomatis, SonarQube | `.github/workflows/ci.yml`, `sonar-project.properties`, `docs/ci-pipeline.md` |
| **2. Perubahan & Kualitas Kode** | 10% | > 3 perubahan signifikan per anggota, zero bugs/vulnerabilities, test cases edge-cases | `docs/laporan-uts-template.md` (Bagian 3) |
| **3. DevSecOps** | 10% | STRIDE Threat Modeling, Dependency Check (`composer`/`npm audit`), GitHub Secrets, Branch Protection | `docs/threat-modeling-stride.md`, `docs/devsecops-and-secret-management.md` |
| **4. Dokumentasi & Laporan** | 10% | Laporan 7 bagian lengkap, terstruktur, retrospektif tim, rencana demo video | `docs/laporan-uts-template.md` |
| **5. Presentasi & Demo** | 5% | Penguasaan materi, demo lancar, kontribusi anggota jelas | `docs/laporan-uts-template.md` (Bagian 7) |
