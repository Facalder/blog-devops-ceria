# Panduan DevSecOps, Secret Management & Branch Protection

Dokumen ini memuat panduan implementasi praktik **DevSecOps** pada proyek Blog DevOps Ceria, mencakup audit otomatis dependensi, kebijakan pengelolaan *secrets*, serta aturan *branch protection* dan *code review*.

---

## 🔒 1. Automated Dependency Security Check di CI Pipeline

Sesuai dengan ketentuan Rubrik 3 UTS DevOps, pipeline GitHub Actions ([`.github/workflows/ci.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci.yml)) dilengkapi dengan proses audit keamanan dependensi otomatis pada ekosistem backend (PHP) dan frontend (Node.js).

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

## 🛡️ 3. Branch Protection & Code Review Rules

Untuk menjamin kualitas dan keamanan kode yang masuk ke branch utama (`main` dan `dev`), repository dikonfigurasi dengan aturan proteksi cabang (*Branch Protection Rules*).

### Konfigurasi Branch Protection pada GitHub:
Buka `Settings -> Branches -> Add branch protection rule` untuk branch `main`:

1. **Require a pull request before merging**:
   - Centang opsi: *Require a pull request before merging*.
   - **Require approvals**: Minimal **1 reviewer** wajib memberikan *approval* sebelum PR dapat di-merge.
   - **Dismiss stale pull request approvals when new commits are pushed**: Aktifkan agar commit baru memerlukan review ulang.
2. **Require status checks to pass before merging**:
   - Centang opsi: *Require status checks to pass before merging*.
   - Centang: *Require branches to be up to date before merging*.
   - Pilih *Status Checks* wajib dari workflow CI:
     - `Code Style & Static Analysis`
     - `Frontend Asset Build`
     - `Automated Testing & Coverage`
     - `DevSecOps Dependency Audit`
3. **Do not allow bypassing the above settings**:
   - Berlaku untuk seluruh anggota tim dan Administrator.
4. **Prevent force pushes** & **Prevent deletions**:
   - Mencegah manipulasi git history pada branch `main`.

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
