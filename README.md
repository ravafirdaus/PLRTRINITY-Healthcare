# PuskesmasFlow — Sistem Reservasi, Antrean Prioritas & Triase Klinis Terpadu

PuskesmasFlow adalah aplikasi sistem informasi antrean cerdas dan manajemen triase berbasis **Laravel** dan **PostgreSQL**. Sistem ini dirancang untuk mengatasi masalah overkapasitas ruang tunggu, antrean tidak teratur, dan keterlambatan penanganan pasien darurat di fasilitas kesehatan tingkat pertama (Puskesmas).

---

## 1. Prasyarat Instalasi

Sebelum menjalankan aplikasi, pastikan perangkat Anda telah terpasang:
- **PHP** versi 8.2 atau lebih tinggi (disarankan PHP 8.4) dengan ekstensi aktif:
  - `pdo_pgsql` & `pgsql`
  - `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`
- **Composer** (v2.x)
- **Node.js** (v18.x atau lebih baru) & **NPM**
- **PostgreSQL** Server (versi 14, 15, 16, 17, atau 18)
- **pgAdmin 4** (untuk administrasi dan pengujian basis data grafis)

---

## 2. Pembuatan Database `puskesmasflow` melalui pgAdmin 4

Ikuti langkah-langkah berikut untuk menyiapkan database di pgAdmin 4:
1. Buka aplikasi **pgAdmin 4**.
2. Masukkan Master Password pgAdmin jika diminta.
3. Di panel sisi kiri (*Servers*), klik dua kali pada server PostgreSQL lokal Anda (misal: `PostgreSQL 16` atau `PostgreSQL 18`) dan masukkan password pengguna `postgres`.
4. Klik kanan pada folder **Databases** &rarr; pilih **Create** &rarr; **Database...**
5. Pada tab *General*, masukkan nama database:
   ```text
   puskesmasflow
   ```
6. Pastikan *Owner* diatur ke `postgres` dan *Encoding* ke `UTF8`.
7. Klik tombol **Save**. Database `puskesmasflow` kini siap digunakan oleh Laravel.

---

## 3. Konfigurasi Kredensial Database di `.env`

Buka file `.env` di direktori utama proyek (`d:\Projects\puskesmasflow\.env`) dan sesuaikan baris koneksi database dengan kredensial PostgreSQL lokal Anda:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=puskesmasflow
DB_USERNAME=postgres
DB_PASSWORD=masukkan_password_postgres_anda_disini
```

> **Catatan Keamanan:** Jangan pernah mengunggah file `.env` yang memuat password asli ke repositori publik (file `.env` telah tercantum di dalam `.gitignore`).

---

## 4. Menjalankan Migrasi dan Seeder

Setelah konfigurasi `.env` sesuai dan database `puskesmasflow` aktif, jalankan perintah Artisan berikut dari terminal:

```powershell
# 1. Jalankan migrasi seluruh tabel
php artisan migrate

# 2. Isi database dengan data konfigurasi, staf, layanan poli, pasien, dan demo
php artisan db:seed

# ATAU jalankan sekaligus dalam satu perintah:
php artisan migrate:fresh --seed
```

Tabel yang dibuat secara otomatis meliputi:
1. `users` (akun staf dan peran hak akses)
2. `clinic_settings` (jam buka/tutup, durasi slot 20 menit, kuota, toleransi keterlambatan)
3. `services` (Poli Umum, Poli Gigi, Poli KIA, Poli Lansia)
4. `patients` (profil pasien minimum)
5. `staff_schedules` (jadwal dokter dan poli)
6. `appointments` (reservasi online terjadwal dengan pencegahan double booking)
7. `queue_entries` (antrean operasional aktif)
8. `triage_assessments` (penilaian klinis triase oleh petugas berwenang)
9. `consultation_sessions` (sesi pelayanan medis oleh dokter)
10. `queue_status_histories` (audit log perubahan status antrean)

---

## 5. Menjalankan Server Aplikasi

Jalankan perintah berikut di terminal:

```powershell
# Jalankan development server Laravel
php artisan serve
```

Aplikasi dapat diakses melalui peramban web di:
```text
http://127.0.0.1:8000
```

Untuk mengompilasi aset frontend Tailwind CSS dan Alpine.js (jika melakukan perubahan tampilan):
```powershell
npm run build
```

---

---

## 6. Akun Demo untuk Pengujian Sistem

Aplikasi menyediakan akun demo bawaan melalui seeder untuk pengujian peran (Role-Based Access Control) dan akun pasien:

| Peran | Kredensial Login | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Pasien Demo** | Nomor HP: `081298765431` | `password` | Login via `/pasien/masuk`, Dashboard Pasien, Reservasi Mandiri tanpa isi ulang profil, Batalkan reservasi, Edit profil |
| **Administrator** | Email: `admin@puskesmasflow.local` | `admin123` | Konfigurasi operasional, manajemen poli, manajemen user, dan seluruh portal |
| **Petugas Pendaftaran** | Email: `pendaftaran@puskesmasflow.local` | `daftar123` | Check-in reservasi, pendaftaran walk-in, cetak struk antrean, pembatalan |
| **Petugas Kesehatan** | Email: `triase@puskesmasflow.local` | `triase123` | Pengisian tanda-tanda vital, penetapan triase (Merah/Kuning/Hijau), aktivasi jalur darurat |
| **Dokter / Petugas Poli** | Email: `dokter@puskesmasflow.local` | `dokter123` | Panggilan antrean prioritas, pelayanan konsultasi, pencatatan disposisi, resep ringkas |

---

## 7. Daftar Fitur yang Berfungsi Penuh

1. **Registrasi & Autentikasi Akun Pasien Terintegrasi (`/pasien/daftar` & `/pasien/masuk`):**
   - **Pendaftaran Sekali Pakai:** Pasien mendaftarkan NIK (16 digit), Nama, Tanggal Lahir, Jenis Kelamin, No. HP, Alamat singkat, dan No. BPJS (opsional) sekali saja.
   - **Normalisasi Nomor HP Otomatis:** Mendukung format input `+628...`, `628...`, dan `08...` yang dinormalisasi ke format standar `08...` sebelum disimpan dan diautentikasi.
   - **Checkbox Persetujuan Data Pribadi:** Dilengkapi klausul persetujuan pemrosesan data untuk pelayanan kesehatan puskesmas.
   - **Autentikasi Berbasis Nomor HP:** Login pasien menggunakan No HP dan password dengan pembatasan percobaan login (*rate limiting*).
   - **Isolasi Role Pasien:** Pasien terisolasi sepenuhnya dari dashboard staf medis (Admin, Loket, Triase, Dokter dilindungi proteksi HTTP 403).

2. **Dashboard & Reservasi Terintegrasi Pasien (`/pasien/dashboard`):**
   - **Penyamaran Identitas (NIK Masking):** NIK ditampilkan dengan sensor keamanan (6 digit awal dan 4 digit akhir, contoh `320101******0001`).
   - **Reservasi Tanpa Pengisian Ulang Data Diri:** Formulir reservasi hanya meminta pemilihan Poli, Tanggal Kunjungan, Slot Waktu, dan Catatan/Keluhan opsional. Data identitas pasien ditarik otomatis dari profil.
   - **Pemilihan Slot Waktu Interaktif:** Alpine.js dinamis yang memuat slot waktu tersedia via AJAX secara real-time.
   - **Pencegahan Double Booking:** Pasien tidak dapat membuat 2 reservasi di poli dan tanggal yang sama.
   - **Pembatalan Reservasi Mandiri:** Pasien dapat membatalkan reservasi berstatus *booked* langsung dari portal.
   - **Pemantauan Antrean Aktif:** Menampilkan status panggilan dan nomor antrean hari ini dengan tombol pembaruan status cepat.

3. **Reservasi Publik & Layar Monitor Antrean Real-Time (`/antrean`):**
   - Pembaruan berkala otomatis via mekanisme polling AJAX tanpa memuat ulang (*refresh*) seluruh halaman.
   - Menampilkan nomor antrean yang sedang dipanggil ke setiap ruangan poli secara jelas.
   - Menampilkan daftar antrean tunggu berikutnya terurut berdasarkan prioritas klinis dan jam estimasi.
   - **Kepatuhan Privasi:** Data sensitif disembunyikan sepenuhnya dari pandangan publik.

4. **Meja Loket Pendaftaran (`/pendaftaran`):**
   - Verifikasi dan check-in reservasi hari ini dengan pemeriksaan batas toleransi keterlambatan (15 menit).
   - Pendaftaran pasien *Walk-in* langsung ke dalam antrean harian tanpa perlu akun online.
   - Pendaftaran Darurat dengan aktivasi tombol Jalur Respons Segera (Prioritas 1 Merah).
   - Penerbitan struk nomor antrean termal (`/pendaftaran/tiket/{id}`).

5. **Meja Penilaian Triase Klinis (`/triase`):**
   - Daftar antrean yang menunggu triase (`waiting_triage`).
   - Penginputan tanda-tanda vital lengkap (Tekanan Darah, Nadi, Laju Pernapasan, Suhu Tubuh, SpO2, Tingkat Kesadaran).
   - **Penetapan Triase Klinis oleh Tenaga Medis Berwenang:** Penetapan kategori Merah, Kuning, Hijau, dan Hitam oleh petugas kesehatan berwenang.
   - Pasien berkategori Merah otomatis memicu pengalihan antrean ke prioritas teratas dokter pemeriksa.

6. **Ruang Pelayanan Medis / Poli Dokter (`/poli`):**
   - Pemilih poli aktif (Poli Umum, Poli Gigi, Poli KIA, Poli Lansia).
   - Pengurutan antrean dinamis: Kasus darurat Merah selalu berada di urutan teratas, diikuti Kuning, kemudian Hijau.
   - Tombol operasional lengkap: Panggil Pasien (*Call*), Mulai Pemeriksaan (*Start*), Selesaikan Pelayanan (*Complete*), dan Lewati Pasien (*Skip*).
   - Form akhir pelayanan: Catatan klinis ringkas dan penentuan disposisi (Pulang, Rawat Inap, Rujuk Lanjutan, Kontrol Ulang).

7. **Pengaturan Sistem Admin (`/admin`):**
   - Konfigurasi jam operasional (buka & tutup).
   - Konfigurasi durasi slot standar (default 20 menit) dan kapasitas per slot.
   - Konfigurasi batas toleransi keterlambatan check-in (menit).
   - Manajemen penambahan dan pembaruan poli layanan.
   - Manajemen akun staf pengguna dan penetapan peran hak akses (*role*).

8. **Laporan Operasional Berbasis Data Nyata (`/laporan`):**
   - Rekapitulasi jumlah pasien terdaftar, selesai, dalam antrean, dilewati, dan dibatalkan.
   - Distribusi per jenis registrasi (Reservasi Online vs Walk-in vs Darurat).
   - Distribusi kategori triase aktual (Merah, Kuning, Hijau, Hitam).
   - Rata-rata durasi waktu konsultasi dokter (menit).

---

## 8. Cara Menjalankan Pengujian (Feature Tests)

Seluruh alur utama telah diuji menggunakan Feature Tests suite:

```powershell
php artisan test
```

Pengujian mencakup:
- `PatientRegistrationTest`: Registrasi pasien baru, validasi NIK 16 digit, penolakan NIK duplikat, kewajiban checkbox persetujuan, dan normalisasi nomor HP (+62xx & 62xx).
- `PatientLoginTest`: Login berbasis No HP & password, normalisasi format No HP pada login, penolakan password salah, proteksi rate limiting, dan logout.
- `PatientReservationTest`: Akses dashboard pasien terotentikasi, pembuatan reservasi tanpa isi ulang profil, pencegahan reservasi ganda per hari, pembatalan reservasi mandiri, dan isolasi privasi antar pasien.
- `AppointmentBookingTest`: Validasi form reservasi, kalkulasi slot, pencegahan pemesanan ganda, dan pencegahan overbooking slot.
- `QueueAndCheckInTest`: Check-in reservasi, pendaftaran walk-in, dan aktivasi antrean darurat.
- `TriageAssessmentTest`: Penilaian tanda vital, penetapan triase hijau vs merah, perubahan prioritas antrean, dan riwayat status.
- `DoctorConsultationTest`: Panggilan antrean dokter, mulai konsultasi, dan penyelesaian pelayanan dengan catatan klinis.
- `RoleAuthorizationTest`: Pembatasan otorisasi antar seluruh peran staf dan penolakan peran pasien dari portal staf (HTTP 403).

Hasil pengujian saat ini: **60 test passed (183 assertions, 100% green)**.

---

## 9. Keterbatasan Prototipe dan Hal yang Belum Terintegrasi

1. **Verifikasi OTP / SMS Nomor Telepon:** Saat ini nomor HP digunakan sebagai identitas akun langsung tanpa pengiriman kode OTP SMS atau WhatsApp Gateway. Dalam lingkungan produksi disarankan menambahkan penyedia SMS OTP (seperti Twilio atau Zenziva).
2. **Integrasi Eksternal (SATUSEHAT & BPJS):** Sesuai ketentuan, aplikasi tidak menghubungkan atau memalsukan integrasi ke sistem SATUSEHAT Kemenkes RI atau BPJS Kesehatan P-Care. Seluruh data pasien yang digunakan adalah data simulasi fiktif.
3. **Rekam Medis Elektronik (RME) Lengkap:** Aplikasi berfokus pada efisiensi antrean, reservasi, estimasi waktu, dan triase operasional. Formulir konsultasi menerapkan prinsip minimisasi data medis untuk kebutuhan alur layanan inti.
