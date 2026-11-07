# 🧭 Master Blueprint Tim Blog DevOps Ceria (6 Anggota)

Dokumen ini merupakan panduan orkestrasi dan pembagian tanggung jawab untuk 6 anggota kelompok pada proyek Ujian Tengah Semester (UTS) **DevOps (CCK4IBB3)**. 

Setiap anggota memiliki **1 workflow DevSecOps khusus** yang menjadi tanggung jawabnya (*Owner / PIC*), serta memiliki kuota **minimal 3 perubahan signifikan** dan **minimal 3 test cases** untuk memenuhi nilai maksimal Rubrik 1 s.d. 5.

---

## 👥 Matriks Pembagian 6 Workflow DevSecOps & Penanggung Jawab

| No | File Blueprint | Penanggung Jawab (PIC) | Workflow DevSecOps yang Dikelola | Fokus Rubrik UTS |
|:---:|:---|:---|:---|:---|
| **1** | [`01-lead-devops-pipeline.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/01-lead-devops-pipeline.md) | **Anggota 1**<br/>*(Lead DevOps)* | [`.github/workflows/devsecops-01-pipeline-integrity.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-01-pipeline-integrity.yml) | **Branch Protection & Pipeline Integrity** (Rubrik 1 & Rubrik 3.d) |
| **2** | [`02-devsecops-security-engineer.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/02-devsecops-security-engineer.md) | **Anggota 2**<br/>*(Backend Security)* | [`.github/workflows/devsecops-02-composer-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-02-composer-audit.yml) | **Backend Dependency Audit (`composer audit`)** (Rubrik 3.b) |
| **3** | [`03-security-engineer-stride-str.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/03-security-engineer-stride-str.md) | **Anggota 3**<br/>*(Frontend Security)* | [`.github/workflows/devsecops-03-npm-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-03-npm-audit.yml) | **Frontend Dependency Audit (`npm audit`)** (Rubrik 3.b) |
| **4** | [`04-security-engineer-stride-ide.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/04-security-engineer-stride-ide.md) | **Anggota 4**<br/>*(Secret Security)* | [`.github/workflows/devsecops-04-secret-scan.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-04-secret-scan.yml) | **Secret Management & Leak Detection (Trivy)** (Rubrik 3.c) |
| **5** | [`05-feature-developer-comments.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/05-feature-developer-comments.md) | **Anggota 5**<br/>*(SAST & Vuln Security)* | [`.github/workflows/devsecops-05-sast-vulnerability.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-05-sast-vulnerability.yml) | **Filesystem Vulnerability & SAST (Trivy CVE)** (Rubrik 3.a & 3.b) |
| **6** | [`06-feature-developer-search-filter.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/06-feature-developer-search-filter.md) | **Anggota 6**<br/>*(STRIDE Compliance)* | [`.github/workflows/devsecops-06-stride-compliance.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-06-stride-compliance.yml) | **STRIDE Threat Mitigation & Security Tests** (Rubrik 3.a & Rubrik 2) |

---

## 🔄 Standar Alur Kerja Tim (Git Flow & Review)

1. **Pull Pembaruan**: Seluruh anggota menarik branch `dev` terbaru.
   ```bash
   git checkout dev
   git pull origin dev
   ```
2. **Buat Branch Fitur/Security**:
   Format: `feat/<nama-fitur>` atau `sec/<nama-keamanan>`.
   ```bash
   git checkout -b sec/composer-audit-hardening
   ```
3. **Commit Sesuai Blueprint**:
   Lakukan minimal 3 commit bermakna dengan format conventional commits (`feat:`, `sec:`, `test:`).
4. **Buka Pull Request (PR) ke `dev`**:
   Seluruh 6 workflow DevSecOps + 4 workflow CI akan otomatis berjalan di GitHub Actions.
5. **Peer Code Review & Approval**:
   Minimal 1 rekan tim lain wajib membuka PR, memeriksa kode, dan memberikan **Approve**.
6. **Merge ke `dev`**:
   Setelah seluruh status checks centang hijau ✅ dan approval terpenuhi, PR dapat digabungkan.
