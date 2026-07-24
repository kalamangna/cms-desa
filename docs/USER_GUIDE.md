# PANDUAN PENGGUNA & OPERATOR (USER_GUIDE.md)

Dokumen ini berisi petunjuk praktis penggunaan aplikasi **Portal Resmi Website & CMS Desa**, baik untuk **Operator Desa (Admin Panel)** maupun untuk **Masyarakat Umum (Portal Publik)**.

---

## 👨‍💼 BAB I: PANDUAN OPERATOR DESA (ADMIN PANEL FILAMENT)

### 1.1 Cara Login ke Panel Admin
1. Akses alamat web admin: `https://domain-desa.id/admin`.
2. Masukkan **Email** dan **Password** operator Anda.
3. Klik tombol **Sign In**.

---

### 1.2 Mengelola Data Mikro Penduduk & Keluarga

#### A. Mengimpor Data dari Berkas Excel Kependudukan (Regsosek/SDGs):
1. Masuk ke menu **Kependudukan** $\rightarrow$ **Keluarga** atau **Penduduk**.
2. Klik tombol **Impor Excel** di sudut kanan atas tabel.
3. Unggah file Excel kuesioner desa Anda (`.xlsx` / `.xls`).
4. Klik **Proses Impor**. Sistem akan secara otomatis mencatat keluarga, individu warga, serta mengklasifikasikan pilihan ke format baku *Title Case*.

#### B. Mengatur Tab & Sub-Section Form Input:
Form admin telah dikelompokkan ke dalam Tab & Sub-Section yang rapi:
- **Form Penduduk**: *Identitas Warga*, *Pendidikan & Pekerjaan*, *Pendapatan & Keuangan*, *Kesehatan & Disabilitas*, dan *Kependudukan*.
- **Form Keluarga**: *Identitas Keluarga*, *Karakteristik Rumah*, *Sanitasi & Utilitas*, serta *Aset & Bantuan*.

---

### 1.3 Konfigurasi Opsi Pembanding Statistik Dinamis (Sumbu Ke-2)
1. Masuk ke menu **Master** $\rightarrow$ **Kategori Statistik**.
2. Edit salah satu kategori (misal: *Pekerjaan* atau *Pendidikan*).
3. Pada bagian **Opsi Pembanding Grafik & Tabel (Sumbu Ke-2)** (`secondary_columns`), centang pilihan pembanding yang ingin diaktifkan untuk publik (misal: *Jenis Kelamin*, *Pendidikan*, *Status Perkawinan*, *Dusun*).
4. Klik **Simpan Changes**. Pilihan pembanding tersebut otomatis dapat diakses publik di dropdown halaman `/statistik`.

---

### 1.4 Mengelola Berita, Pengumuman, Galeri, & APBDes
- **Berita & Kegiatan**: Unggah gambar sampul berita. Gambar akan otomatis dikompresi di bawah 300 KB agar pratinjau tautan WhatsApp tampil sempurna.
- **Pengumuman Resmi**: Gunakan fitur deskripsi cepat untuk pengumuman yang tidak memerlukan halaman detail terpisah.
- **Transparansi APBDes**: Input target dan realisasi pendapatan/belanja per tahun anggaran.

---

## 🌐 BAB II: PANDUAN MASYARAKAT (PORTAL PUBLIK)

### 2.1 Navigasi Dashboard Statistik Interaktif (`/statistik`)
1. **Memilih Kategori**: Klik nama kategori di sidebar desktop atau dropdown mobile.
2. **Memilih Pembanding (Grafik 2 Arah)**: Gunakan dropdown `[ Pembanding: ... ▾ ]` di atas grafik untuk memecah data (misal: melihat Pekerjaan berdasarkan Pendidikan atau Gender). Grafik akan otomatis berubah menjadi *Horizontal Stacked Bar Chart*.
3. **Mengekspor Tabel Resmi**: Klik tombol **Ekspor** di kanan atas tabel, lalu pilih format berkas (**CSV**, **Excel (XLSX)**, atau **PDF**). Berkas yang terunduh otomatis menyertakan Kop Header Resmi Pemerintah Desa.

---

### 2.2 Layanan Mandiri Permohonan Surat Online (`/layanan-mandiri`)
1. Buka halaman **Layanan Mandiri**.
2. Isikan NIK, Jenis Surat yang dibutuhkan (misal: *Surat Keterangan Usaha*), Keperluan, dan Nomor HP (WhatsApp).
3. Klik **Kirim Permohonan**.
4. Catat **Nomor Tiket** yang muncul di layar untuk melacak progress permohonan surat Anda.

---

### 2.3 Pengaduan Warga Online (`/pengaduan`)
1. Buka halaman **Pengaduan**.
2. Isikan Nama, Nomor Kontak, Kategori Aduan, dan Deskripsi Pengaduan.
3. Klik **Kirim Pengaduan**. Laporan akan langsung diteruskan ke dashboard Operator Desa untuk ditindaklanjuti.
