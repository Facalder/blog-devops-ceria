# Panduan DevSecOps, Secret Management & Branch Protection

Dokumen ini memuat panduan implementasi praktik **DevSecOps** pada proyek Blog DevOps Ceria, mencakup audit otomatis dependensi, kebijakan pengelolaan *secrets*, serta aturan *branch protection* dan *code review*.

---

## 🔒 1. Automated Dependency Security Check di CI Pipeline

Sesuai dengan ketentuan Rubrik Penilaian UTS DevOps, pipeline keamanan dan kualitas kode dipetakan secara jelas ke 6 penanggung jawab (*Person in Charge*):
1. [`.github/workflows/devsecops-branch-protection.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-branch-protection.yml) - PIC: Anggota 1 (Integritas pipeline, aturan cabang, dan zero-leak config).
2. [`.github/workflows/devsecops-composer-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-composer-audit.yml) - PIC: Anggota 2 (Audit kerentanan dependensi backend PHP via `composer audit`).
3. [`.github/workflows/devsecops-npm-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-npm-audit.yml) - PIC: Anggota 3 (Audit kerentanan dependensi frontend Node.js via `npm audit`).
4. [`.github/workflows/devsecops-secret-scan.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-secret-scan.yml) - PIC: Anggota 4 (Pemindaian kebocoran kredensial dan API keys via Trivy secret scanner).
5. [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml) - PIC: Anggota 5 (Analisis kualitas kode SonarQube & evaluasi Quality Gate).
6. [`.github/workflows/devsecops-stride-tests.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-stride-tests.yml) - PIC: Anggota 6 (Otomasi test suite kepatuhan dan mitigasi ancaman STRIDE).

### A. PHP Dependency Audit (`composer audit`)
- **Fungsi**: Memeriksa file `composer.lock` terhadap database kerentanan resmi *FriendsOfPHP Security Advisories* dan *GitHub Security Advisory Database*.
- **Perintah di Pipeline**:
  ```bash
  composer audit --locked
  ```
- **Kategori Temuan & Mitigasi**:
  Jika ditemukan *vulnerability*, developer wajib mengevaluasi apakah perbaikan tersedia dengan menaikkan minor/patch version dependensi:
  ```bash
  composer update <vendor/package>
  ```

### B. JavaScript / Node.js Dependency Audit (`npm audit`)
- **Fungsi**: Memindai seluruh *tree package* di `package-lock.json` untuk mendeteksi kerentanan pada pustaka frontend.
- **Perintah di Pipeline**:
  ```bash
  npm audit --audit-level=high
  ```
- **Penanganan Kerentanan**:
  ```bash
  npm audit fix
  ```

### C. Repository Vulnerability & Secret Scanning (`Trivy`)
- **Fungsi**: Melakukan pemindaian mendalam (*deep scan*) pada filesystem repositori untuk mendeteksi kerentanan CVE pada seluruh pustaka serta mendeteksi kebocoran kredensial/token (*secret scanning*).
- **Implementasi di Pipeline**:
  ```yaml
  - name: Run Trivy Security & Vulnerability Scanner
    uses: aquasecurity/trivy-action@v0.36.0
    continue-on-error: true
    with:
      scan-type: 'fs'
      scan-ref: '.'
      scanners: 'vuln,secret'
      severity: 'CRITICAL,HIGH'
      format: 'table'
      exit-code: '0'
  ```

---

## 🔑 2. Kebijakan Secret Management

### Prinsip Utama: Zero Hardcoded Credentials
Tidak diperkenankan menyimpan kredensial sensitif (*API key*, *database password*, *secret tokens*, *encryption key*) langsung di dalam source code repository.

### Mekanisme Pengelolaan:
1. **File `.env.example`**:
   - Berisi daftar variabel environment yang dibutuhkan tanpa menyertakan nilai rahasia asli.
   - Di-commit ke dalam Git sebagai acuan konfigurasi lingkungan.
2. **File `.env`**:
   - Menyimpan nilai rahasia aktual untuk keperluan development lokal.
   - **Wajib di-ignore** pada file [`.gitignore`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.gitignore) agar tidak ter-push ke GitHub.
3. **GitHub Secrets pada Lingkungan CI/CD**:
   Seluruh token dan konfigurasi rahasia yang dibutuhkan oleh GitHub Actions disimpan melalui menu:
   `Repository Settings -> Secrets and variables -> Actions -> Repository Secrets`.

### Daftar GitHub Secrets yang Diperlukan:

| Nama Secret | Deskripsi / Penggunaan | Status |
|---|---|:---:|
| `APP_KEY` | Laravel application encryption key (32 char base64) | Wajib jika pipeline menggunakan enkripsi session |
| `SONAR_TOKEN` | Token autentikasi untuk pengiriman laporan ke SonarQube / SonarCloud | Wajib untuk job `sonarqube` |
| `SONAR_HOST_URL` | URL instance SonarQube (cth: `https://sonarcloud.io` atau internal server) | Wajib untuk job `sonarqube` |

---

## 🛡️ 3. Branch Protection & Ruleset Configuration (Branch `main` & `dev`)

Repositori ini telah menyediakan konfigurasi GitHub Rulesets berbasis kode (*Rulesets-as-Code*) di dalam direktori [`.github/rulesets/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets) serta skrip otomasi API di [`scripts/setup-rulesets.sh`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/scripts/setup-rulesets.sh).

### A. Rincian Konfigurasi Ruleset per Branch

| Parameter Aturan | Branch `main` (Produksi & Rilis) | Branch `dev` (Integrasi & Staging) |
|---|---|---|
| **File Konfigurasi** | [`.github/rulesets/main-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/main-ruleset.json) | [`.github/rulesets/dev-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/dev-ruleset.json) |
| **Target Cabang** | `refs/heads/main` | `refs/heads/dev` |
| **Status Enforcement** | `active` | `active` |
| **Restrict Deletions** | Aktif (Mencegah penghapusan cabang) | Aktif (Mencegah penghapusan cabang) |
| **Restrict Force Pushes** | Aktif (Mencegah rewrite history `git push --force`) | Aktif (Mencegah rewrite history `git push --force`) |
| **Require Linear History** | Aktif (Mewajibkan rebase/squash merge) | Non-aktif (Boleh merge commit) |
| **Pull Request Requirements** | Minimal **1 review approval**, dismiss stale reviews on push, require last push approval, resolve threads | Minimal **1 review approval**, dismiss stale reviews on push, resolve threads |
| **Metode Merge Diizinkan** | `squash`, `rebase` | `merge`, `squash`, `rebase` |
| **Required Status Checks** | 1. `Code Style & Static Analysis`<br/>2. `Frontend Asset Build`<br/>3. `DevSecOps Dependency Audit`<br/>4. `Automated Testing & Coverage`<br/>5. `SonarCloud Code Quality Analysis` | 1. `Code Style & Static Analysis`<br/>2. `Frontend Asset Build`<br/>3. `DevSecOps Dependency Audit`<br/>4. `Automated Testing & Coverage` |
| **Strict Up-to-Date Policy** | Aktif (Wajib update branch sebelum merge) | Aktif (Wajib update branch sebelum merge) |
| **Bypass Rules** | **Tidak ada bypass** (Berlaku ketat bagi admin & owner) | Repository Admin (Bypass mode always untuk darurat) |

### B. Cara Menerapkan Ruleset ke Repositori GitHub

Tersedia dua metode penerapan yang dapat dipilih:

#### Metode 1: Import File JSON via GitHub Web UI (Direkomendasikan)
1. Buka repositori GitHub: `https://github.com/Facalder/blog-devops-ceria`
2. Masuk ke tab **Settings** -> pilih menu **Rules** -> klik **Rulesets**.
3. Klik tombol dropdown hijau **New ruleset** di pojok kanan atas, lalu pilih **Import a ruleset**.
4. Pilih file [`.github/rulesets/main-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/main-ruleset.json). Seluruh form akan otomatis terisi.
5. Klik **Create** untuk mengaktifkan ruleset branch `main`.
6. Ulangi langkah di atas untuk mengimpor [`.github/rulesets/dev-ruleset.json`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/rulesets/dev-ruleset.json).

#### Metode 2: Penerapan Otomatis via API Script
Jalankan skrip yang telah disiapkan dengan menyertakan GitHub Personal Access Token (PAT) yang memiliki permission `Administration: Read & Write`:
```bash
GITHUB_TOKEN="ghp_your_personal_access_token_here" ./scripts/setup-rulesets.sh
```

---

### Standar Alur Kerja Tim (Git Flow):
```text
Feature Branch (feature/nama-fitur)
        │
        ├──> Commit perubahan kode & unit test
        ├──> Push ke remote repository
        ├──> Buka Pull Request (PR) ke branch `dev` / `main`
        ├──> GitHub Actions CI Pipeline berjalan otomatis
        ├──> Code Review & Approval oleh minimal 1 rekan tim
        └──> Merge ke `main` setelah seluruh check berstatus Green (Passing)
```
