# 📚 Dokumentasi Master: Blog DevOps Ceria

Dokumentasi ini merupakan pusat panduan arsitektur, operasional tim, konfigurasi CI/CD, dan tata kelola **DevSecOps** pada proyek **Ujian Tengah Semester (UTS) Mata Kuliah DevOps (CCK4IBB3)** Program Studi S1 Rekayasa Perangkat Lunak, Telkom University.

---

## 🗂️ Struktur Folder Dokumentasi (`/docs`)

```text
docs/
├── README.md                           # Indeks master & navigasi seluruh dokumentasi
├── team-guide/                         # Panduan kolaborasi, alur kerja, & perencanaan tim
│   ├── panduan-workflow-dan-kolaborasi-tim.md
│   └── ci-cd-planning.md
├── blueprints/                         # Blueprint individual tugas 6 anggota tim
│   ├── README.md
│   ├── 01-lead-devops-pipeline.md
│   ├── 02-devsecops-security-engineer.md
│   ├── 03-security-engineer-stride-str.md
│   ├── 04-security-engineer-stride-ide.md
│   ├── 05-feature-developer-comments.md
│   └── 06-feature-developer-search-filter.md
├── ci-cd/                              # Arsitektur CI/CD, testing, & code quality
│   ├── ci-pipeline.md
│   └── sonarqube-and-testing.md
├── devsecops/                          # Keamanan aplikasi, STRIDE, audit, & secrets
│   ├── devsecops-and-secret-management.md
│   └── threat-modeling-stride.md
└── reports/                            # Template laporan proyek & outline presentasi
    └── laporan-uts-template.md
```

---

## 📖 Katalog & Akses Cepat Dokumen

### 1. 👥 Panduan Kolaborasi Tim (`docs/team-guide/`)
- [**Panduan Workflow & Kolaborasi Tim**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/team-guide/panduan-workflow-dan-kolaborasi-tim.md):  
  Alur Git Flow dari laptop lokal ke Pull Request, otomasi 10 workflow GitHub Actions di cloud runner, siklus auto re-run saat ada error, prosedur peer review, dan template pesan WhatsApp tim.
- [**Rencana Strategis & Master Plan CI/CD**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/team-guide/ci-cd-planning.md):  
  Cetak biru perencanaan awal, target rubrik skor 4, konvensi commit, dan aturan operasional ketat (*Strict Rules*).

### 2. 📋 Master Blueprints 6 Anggota (`docs/blueprints/`)
- [**Master Blueprints Index**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/README.md):  
  Matriks pembagian peran di mana setiap anggota memegang tanggung jawab atas 1 workflow DevSecOps khusus, minimal 3 perubahan aplikasi/pipeline, dan minimal 3 test cases.
  - [Blueprint 1: Lead DevOps & Branch Protection Architect](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/01-lead-devops-branch-protection.md)
  - [Blueprint 2: Backend PHP Composer Audit Engineer](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/02-backend-dependency-audit.md)
  - [Blueprint 3: Frontend Node NPM Audit Engineer](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/03-frontend-dependency-audit.md)
  - [Blueprint 4: Secret Leak Detection Engineer](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/04-secret-management-security.md)
  - [Blueprint 5: Code Quality & SonarQube Engineer](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/05-sonarqube-code-quality.md)
  - [Blueprint 6: STRIDE Threat Modeling Security Engineer](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/06-stride-threat-modeling.md)

### 3. ⚙️ Arsitektur CI/CD & Testing (`docs/ci-cd/`)
- [**Arsitektur CI Pipeline Modular**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/ci-cd/ci-pipeline.md):  
  Dokumentasi 4 workflow CI terisolasi: linting PSR-12 & static analysis Level 7 ([`ci-code-quality.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-code-quality.yml)), kompilasi aset frontend ([`ci-frontend-build.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-frontend-build.yml)), automated testing dengan PCOV coverage ([`ci-testing.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/ci-testing.yml)), dan evaluasi Quality Gate ([`sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml)).
- [**SonarQube & Strategi Testing**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/ci-cd/sonarqube-and-testing.md):  
  Konfigurasi parameter `sonar-project.properties`, pemetaan direktori pengecualian (*exclusions*), dan strategi pembuatan test cases Pest 5 dengan target coverage > 50%.

### 4. 🛡️ DevSecOps & Keamanan Aplikasi (`docs/devsecops/`)
- [**Panduan DevSecOps & Secret Management**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/devsecops-and-secret-management.md):  
  Dokumentasi workflow DevSecOps terpadu, scanner dependensi (`composer audit` & `npm audit`), deteksi kebocoran credential (`Trivy`), pengujian ancaman STRIDE, serta aturan Branch Protection GitHub Rulesets.
- [**Threat Modeling STRIDE**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/threat-modeling-stride.md):  
  Analisis ancaman keamanan komprehensif pada 6 pilar (*Spoofing*, *Tampering*, *Repudiation*, *Information Disclosure*, *Denial of Service*, *Elevation of Privilege*) yang dipetakan ke komponen web Laravel.

### 5. 📝 Laporan Proyek & Presentasi (`docs/reports/`)
- [**Template Laporan Proyek UTS**](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/reports/laporan-uts-template.md):  
  Format resmi laporan proyek yang mencakup 7 bagian wajib sesuai ketentuan dosen, tabel kontribusi 6 anggota, dan outline menit-ke-menit video YouTube.

---

## 📊 Matriks Pemetaan Kriteria Penilaian UTS (Bobot 50%)

| Komponen Rubrik | Bobot | Kriteria Target (Skor 4 - Sangat Baik) | Dokumen & File Implementasi |
|---|:---:|---|---|
| **1. Pipeline CI** | 15% | Multi-stage otomatis (Build, Test, Analysis), Best-practice YAML, Test terintegrasi, SonarCloud Gate | [`docs/ci-cd/ci-pipeline.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/ci-cd/ci-pipeline.md), `.github/workflows/ci-*.yml` |
| **2. Perubahan & Kualitas Kode** | 10% | > 3 perubahan signifikan per anggota (6 orang), 0 bugs/vulnerabilities, > 3 test cases per fitur | [`docs/blueprints/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/), [`docs/reports/laporan-uts-template.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/reports/laporan-uts-template.md) |
| **3. DevSecOps** | 10% | Analisis STRIDE lengkap, Dependency Check (Composer & NPM), Secret Management (Trivy), Branch Protection | [`docs/devsecops/threat-modeling-stride.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/devsecops/threat-modeling-stride.md), `.github/workflows/devsecops-*.yml` |
| **4. Dokumentasi & Laporan** | 10% | Laporan 7 bagian lengkap & terstruktur, outline video demo YouTube, retrospektif tim mendalam | [`docs/reports/laporan-uts-template.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/reports/laporan-uts-template.md) |
| **5. Presentasi & Demo** | 5% | Penguasaan materi, demonstrasi lancar, kontribusi individu terlihat jelas | [`docs/blueprints/`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/), [`docs/team-guide/panduan-workflow-dan-kolaborasi-tim.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/team-guide/panduan-workflow-dan-kolaborasi-tim.md) |

---

## 🛠️ Perintah Esensial Developer

- **Setup Awal Proyek**:
  ```bash
  composer setup
  ```
- **Menjalankan Server Pengembangan (Server + Queue + Vite Plus)**:
  ```bash
  composer dev
  ```
- **Validasi Kualitas Kode & Testing Lokal (CI Full Gate Check)**:
  ```bash
  composer ci:check
  ```
- **Audit Keamanan Dependensi Lokal**:
  ```bash
  composer audit --locked
  npm audit --audit-level=high
  ```
