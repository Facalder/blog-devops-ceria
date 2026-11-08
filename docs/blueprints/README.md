# 🧭 Master Blueprint Tim Blog DevOps Ceria (6 Anggota)

Dokumen ini merupakan panduan orkestrasi dan pembagian tanggung jawab untuk 6 anggota kelompok pada proyek Ujian Tengah Semester (UTS) **DevOps (CCK4IBB3)**. 

Setiap anggota memiliki **1 workflow DevSecOps khusus** yang menjadi tanggung jawabnya (*Owner / PIC*), serta memiliki kuota **minimal 3 perubahan signifikan** dan **minimal 3 test cases** untuk memenuhi nilai maksimal Rubrik 1 s.d. 5.

---

## 👥 Matriks Pembagian 6 Workflow DevSecOps & Penanggung Jawab

| No | File Blueprint | Penanggung Jawab (PIC) | Workflow / Komponen yang Dikelola | Fokus Rubrik UTS |
|:---:|:---|:---|:---|:---|
| **1** | [`01-lead-devops-branch-protection.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/01-lead-devops-branch-protection.md) | **Anggota 1**<br/>*(Lead DevOps)* | [`.github/workflows/devsecops-branch-protection.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-branch-protection.yml) | **Branch Protection & Pipeline Architecture** (Rubrik 1 & Rubrik 3.d) |
| **2** | [`02-backend-dependency-audit.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/02-backend-dependency-audit.md) | **Anggota 2**<br/>*(Backend Security)* | [`.github/workflows/devsecops-composer-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-composer-audit.yml) | **Backend Dependency Audit (`composer audit`)** (Rubrik 3.b) |
| **3** | [`03-frontend-dependency-audit.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/03-frontend-dependency-audit.md) | **Anggota 3**<br/>*(Frontend Security)* | [`.github/workflows/devsecops-npm-audit.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-npm-audit.yml) | **Frontend Dependency Audit (`npm audit`)** (Rubrik 3.b) |
| **4** | [`04-secret-management-security.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/04-secret-management-security.md) | **Anggota 4**<br/>*(Secret Security)* | [`.github/workflows/devsecops-secret-scan.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-secret-scan.yml) | **Secret Management & Leak Detection (Trivy)** (Rubrik 3.c) |
| **5** | [`05-sonarqube-code-quality.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/05-sonarqube-code-quality.md) | **Anggota 5**<br/>*(Quality Gate Engineer)* | [`.github/workflows/sonarcloud.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/sonarcloud.yml) | **SonarQube Quality Gate & Code Quality** (Rubrik 1 Poin 4 & Rubrik 2 Poin 2) |
| **6** | [`06-stride-threat-modeling.md`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/docs/blueprints/06-stride-threat-modeling.md) | **Anggota 6**<br/>*(STRIDE Threat Modeling)* | [`.github/workflows/devsecops-stride-tests.yml`](file:///Volumes/Caldera/CALDERA/PRIBADI/DevOps/blog-devops-ceria/.github/workflows/devsecops-stride-tests.yml) | **STRIDE Threat Modeling Automated Tests** (Rubrik 3.a & Rubrik 2) |

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
