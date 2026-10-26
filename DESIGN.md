# Design System: Zed Dev Light

Design guide untuk Blog & CMS Dashboard. Mengadopsi estetika editor-forward Zed: tipografi editorial (serif headline + monospace/writer body), palet warna presisi dengan aksen elektrik blue, border tipis, elevasi datar/minim bayangan, dan sudut radius tajam (2px–4px).

---

## 1. Tokens & Color Palette

| Token | Hex | Tailwind Utility / CSS Var | Deskripsi & Penggunaan |
|---|---|---|---|
| **Primary** | `#1348DC` | `bg-[#1348DC]`, `text-[#1348DC]` | Aksen signature elektrik blue untuk tombol aksi utama, link aktif, highlight. |
| **Primary Hover** | `#3660DE` | `hover:bg-[#3660DE]` | State hover elemen primary. |
| **Primary Active** | `#0F39AD` | `active:bg-[#0F39AD]` | State pressed/fokus elemen primary. |
| **Primary Subtle** | `#5F83E6` | `text-[#5F83E6]` | Aksen sekunder / border highlight lembut. |
| **Primary Tint** | `#EEF2FD` | `bg-[#EEF2FD]` | Background badge status / active selection. |
| **Neutral (Canvas)**| `#FFFFFF` | `bg-white` | Kanvas utama halaman dan surface card. |
| **Surface** | `#F7F8FA` | `bg-[#F7F8FA]` | Background pemisah (sidebar, header bar, code blocks). |
| **On-Surface (Text)**| `#242529` | `text-[#242529]` | Warna teks utama (deep graphite), tajam tanpa kontras hitam pekat. |
| **Tertiary (Muted)**| `#6F7B90` | `text-[#6F7B90]` | Metadata, label sekunder, timestamp, author date, border subtle. |
| **Border Subtle** | `#E5E7EB` | `border-[#E5E7EB]` | Border tipis 1px untuk cards, divider, dan input fields. |
| **Error** | `#E45757` | `text-[#E45757]`, `bg-[#E45757]` | Pesan validasi, badge status draft/error, tombol aksi destruktif. |

---

## 2. Typography

Karakter: Kombinasi **Serif ringan** untuk judul artikel/hero (kesan editorial elegan) dan **Sans/Writer monospace-leaning** berjarak rapat untuk isi konten, label, kontrol form.

| Level | Font Family | Size / Line-height | Tracking & Weight | Penggunaan |
|---|---|---|---|---|
| **Headline Display** | IBM Plex Serif / Serif | `text-4xl` / `text-5xl` (48px / 58px) | Light (300-400), `-0.02em` | Judul hero public blog, headline cover artikel |
| **Headline Lg** | IBM Plex Serif / Serif | `text-3xl` (36px / 44px) | Light (300-400), normal | Judul detail artikel blog, header halaman CMS |
| **Headline Md** | IBM Plex Serif / Serif | `text-2xl` (28px / 34px) | Regular (400), normal | Judul section, heading card artikel di list |
| **Headline Sm** | Sans / Mono / Writer | `text-xl` (20px / 26px) | Medium (500), normal | Sub-heading dalam artikel, title modal CMS |
| **Body Lg** | Sans / Writer | `text-lg` (18px / 28px) | Light/Regular, `-0.01em` | Intro paragraf artikel blog |
| **Body Md** | Sans / Writer | `text-base` (16px / 24px) | Regular (400), `-0.025em`| Isi artikel markdown, deskripsi, form copy |
| **Body Sm** | Sans / Writer | `text-sm` (14px / 21px) | Regular (400), normal | Tabel CMS list artikel, helper text |
| **Label Sm/Md** | Sans / Mono | `text-xs` (11px-12px) | Medium (500), `+0.02em` | Metadata, tags, pill status, navigation link |

---

## 3. Shapes & Spacing

- **Radius:**
  - `rounded-none`: 0px (Divider, tertiary button)
  - `rounded-sm`: 2px (Card list, modal container, badge kotak, form input)
  - `rounded`: 4px (`rounded-md` standard: tombol, input focus ring)
  - `rounded-lg`: 8px (Container preview besar)
  - `rounded-full`: 9999px (Khusus chip tag kategori, avatar)
- **Elevasi & Depth:**
  - Flat & border-driven.
  - Hindari drop shadow pekat. Gunakan border 1px `border-[#E5E7EB]` atau shadow tipis `shadow-xs`/`shadow-sm`.
- **Rhythm Spacing:**
  - Kelipatan 10px, 20px, 24px, 48px (`gap-2.5`, `gap-5`, `p-5`, `py-12`).

---

## 4. UI Components Specification

### Button Primary
- Background: `#1348DC`, Text: `#FFFFFF`
- Radius: `rounded-[4px]`, Height: `36px` (`h-9 px-3.5`)
- Font: `text-sm font-medium`, Hover: `#3660DE`, Active: `#0F39AD`

### Button Secondary / Ghost
- Background: `transparent`, Text: `#242529`
- Border: `border border-[#E5E7EB] hover:bg-[#F7F8FA]`
- Radius: `rounded-[4px]`, Height: `36px`

### Form Input & Markdown Editor Area
- Background: `#FFFFFF`, Text: `#242529`
- Border: `1px solid #E5E7EB`, Focus: `border-[#1348DC] ring-1 ring-[#1348DC]`
- Padding: `10px 12px`, Radius: `rounded-[2px]`, Font: `text-sm font-mono` untuk markdown editor

### Article Card (Global View List)
- Background: `#FFFFFF`, Border: `1px solid #E5E7EB`
- Hover state: Border berubah ke `#1348DC` tipis atau background `#F7F8FA` halus tanpa shadow melayang
- Padding: `p-5 md:p-6`, Radius: `rounded-[2px]`
- Judul: Serif font, Hover warna aksen `#1348DC`
- Metadata: Tag kategori dalam Chip, tanggal dan reading time warna `#6F7B90`

### Chip / Pill
- Background: `#F7F8FA`, Border: `1px solid #E5E7EB`
- Text: `#242529`, Font: `text-xs font-mono tracking-wide`
- Radius: `rounded-full`, Padding: `py-0.5 px-2.5`

---

## 5. View Structures

### View A: Global View (Public Readers)
- **Top Navigation**: Logo teks minimalis, link artikel/kategori, tombol Login/Dashboard untuk Writer.
- **Hero / Header**: Headline serif ringan berpusat ("Notes, Thoughts & Code"), spacing luas (`py-16`).
- **Article Feed**: Layout clean 1 kolom atau grid 2 kolom dengan metadata rapi (kategori chip, tanggal, estimasi baca).
- **Article Single**: Layout membaca terfokus (max-width `prose-lg` / `max-w-3xl`), typography serif pada judul, typography markdown yang bersih untuk heading h1-h4, code blocks, quote, dan gambar.

### View B: Dashboard CMS (Writer Only)
- **Header & Navigation**: Toolbar minimalis di atas surface `#F7F8FA`. Quick stats / list artikel milik writer.
- **Content Management Table**: Tabel artikel yang rapi (Title, Status: Draft/Published, Tanggal Dibuat, Aksi: Edit/Delete/Preview).
- **Markdown Editor**:
  - Split-view atau tabbed view: Textarea markdown (font monospace/writer) di kiri + Live preview dengan styling Zed di kanan.
  - Input judul serif elegan, slug auto-generate, selector status & kategori.
  - Aksi: "Save as Draft", "Publish Article" (Button Primary `#1348DC`).

---

## 6. Do's and Don'ts

- **DO** pertahankan warna aksen elektrik blue `#1348DC` hanya untuk call-to-action utama dan tautan aktif.
- **DO** gunakan font Serif ringan pada judul dan sans/monospace bersih pada konten.
- **DO** gunakan border halus 1px ketimbang shadow tebal untuk memisahkan section.
- **DON'T** menggunakan border-radius besar/bulat pada kartu, tombol, atau modal (kecuali chip pill).
- **DON'T** memakai gradien warna-warni atau efek glassmorphism yang merusak karakter minimal editor.
