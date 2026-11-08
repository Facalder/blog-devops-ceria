# 📋 Blueprint Anggota 1: Lead DevOps & Pipeline Integrity Architect

## 🎯 Identitas Peran
- **Peran**: Lead DevOps & Access Control Architect
- **Penanggung Jawab Workflow DevSecOps**: [`.github/workflows/devsecops-branch-protection.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-branch-protection.yml)
- **Fokus Utama**: Merancang dan memelihara arsitektur CI/CD terpusat, pengujian otomatis, analisis statis, integrasi SonarCloud Quality Gate, dan tata kelola cabang (Branch Protection).
- **Target Skor Rubrik**: Rubrik 1 (15% - Sangat Baik), Rubrik 3 Poin 4 (Branch Protection & Code Review).

---

## 🛠️ File Kerja & Tanggung Jawab
- [`.github/workflows/ci-code-quality.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-code-quality.yml)
- [`.github/workflows/ci-frontend-build.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-frontend-build.yml)
- [`.github/workflows/ci-testing.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-testing.yml)
- [`.github/workflows/devsecops-branch-protection.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-branch-protection.yml)
- [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml)
- [`.github/rulesets/main-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/main-ruleset.json)
- [`.github/rulesets/dev-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/dev-ruleset.json)
- [`scripts/setup-rulesets.sh`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/scripts/setup-rulesets.sh)
- [`sonar-project.properties`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/sonar-project.properties)

---

## 📌 Rincian 3 Perubahan Signifikan (Untuk Riwayat Commit & Laporan)

1. **Perubahan 1: Multi-Stage Modular CI Pipeline & Caching Optimization**
   - **File**: `.github/workflows/ci-code-quality.yml`, `.github/workflows/ci-frontend-build.yml`, `.github/workflows/ci-testing.yml`
   - **Deskripsi Teknis**: Membangun workflow GitHub Actions terpisah dan terisolasi untuk lint/static analysis, build aset frontend, dan automated testing. Mengonfigurasi caching otomatis pada direktori `vendor` dan `node_modules`.
   - **Pesan Commit Contoh**: `ci: build modular multi-stage pipelines with composer and npm cache layers`

2. **Perubahan 2: Integrasi SonarCloud Quality Gate & PCOV Coverage Ingestion**
   - **File**: `sonar-project.properties`, `.github/workflows/sonarcloud.yml`
   - **Deskripsi Teknis**: Mengonfigurasi driver PCOV untuk menghasilkan laporan `coverage.xml` (Clover XML format) dan menghubungkannya dengan SonarCloud Scanner untuk evaluasi Quality Gate.
   - **Pesan Commit Contoh**: `ci: configure sonarcloud integration and pcov clover coverage ingestion`

3. **Perubahan 3: Konfigurasi Ruleset Branch Protection & Pipeline Integrity Workflow**
   - **File**: `.github/workflows/devsecops-branch-protection.yml`, `.github/rulesets/main-ruleset.json`, `scripts/setup-rulesets.sh`
   - **Deskripsi Teknis**: Menerapkan workflow verifikasi integritas pipeline dan ruleset cabang yang mewajibkan Pull Request dengan minimal 1 approval reviewer independen sebelum merge.
   - **Pesan Commit Contoh**: `sec: implement devsecops pipeline integrity workflow and branch protection rulesets`

---

## 🧪 Validasi Pengujian & Kualitas
- Menjalankan pipeline lokal sebelum push:
  ```bash
  vendor/bin/pint --test
  vendor/bin/phpstan analyse --debug
  vendor/bin/pest --coverage
  ```
- Memverifikasi seluruh 10 status checks berstatus hijau pada pull request di GitHub.

---

## 📝 Materi untuk Laporan Proyek & Video Presentasi
- **Laporan Bab 2 & Bab 6**: Menjelaskan arsitektur workflow, tahapan jobs, aturan Branch Protection, dan screenshot keberhasilan build.
- **Video Presentasi (Durasi 2 Menit)**: Mendemonstrasikan trigger otomatis saat ada push/PR, ruleset proteksi branch, dan Quality Gate SonarCloud.
