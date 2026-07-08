# Tracer Study Alumni — SMKN 3 Pontianak

Sistem informasi pelacakan alumni (tracer study) berbasis web untuk SMKN 3 Pontianak. Dibangun dengan **Laravel 12**, **Livewire 4**, dan **Tailwind CSS v4**.

---

## Fitur Aplikasi

### 👤 Alumni
- **Klaim Akun** — alumni mendaftarkan diri dan menghubungkan ke data siswa yang sudah diinput admin.
- **Formulir Tracer Study** — pengisian kuesioner lengkap A–G secara bertahap:
  - A: Data domisili & kontak
  - B: Status kegiatan (bekerja / kuliah / belum bekerja)
  - C: Detail pekerjaan
  - D: Detail studi lanjut
  - E: Aktivitas bagi yang belum bekerja
  - F: Detail usaha / wirausaha
  - G: Penilaian SMK & PKL
- **Simpan Draft** — progress isian tersimpan dan bisa dilanjutkan kapan saja.
- **Dashboard Alumni** — status tracer, riwayat pengisian, dan jadwal pengisian berikutnya.

### 🛠️ Admin
- **Manajemen Data Alumni** — tambah, edit, impor via Excel, dan ekspor data alumni.
- **Manajemen Pengguna** — kelola akun pengguna dengan role-based access (admin, alumni, dll).
- **Laporan Alumni** — statistik jumlah alumni per tahun lulus, jenis kelamin, usia, dan status klaim akun. Ekspor ke PDF.
- **Laporan Tracer Study** — statistik per periode, status kegiatan, jenis instansi, keselarasan bidang pekerjaan & studi, rata-rata gaji, kampus favorit. Ekspor ke PDF & Excel.
- **Pengaturan Aplikasi** — nama sekolah, alamat, dan konfigurasi umum.
- **Data Referensi** — master data kompetensi/jurusan, provinsi, kota, universitas, dan negara.

### 🌐 Halaman Publik
- Landing page dengan statistik ringkas alumni (total, bekerja, wirausaha, studi lanjut).
- Statistik dinamis: jenis instansi, keselarasan pekerjaan & studi, kampus favorit.
- Gambar banner responsif (tampilan berbeda untuk desktop & mobile).

---

## Persyaratan Sistem

| Komponen | Versi Minimum |
|---|---|
| **PHP** | 8.2 |
| **Composer** | 2.x |
| **Node.js** | 18.x |
| **npm** | 9.x |
| **Database** | SQLite 3 / MySQL 8 / MariaDB 10.4 |

### PHP Extensions yang Dibutuhkan
- `pdo`, `pdo_sqlite` atau `pdo_mysql`
- `mbstring`
- `xml`
- `zip` (untuk ekspor Excel)
- `gd` atau `imagick` (untuk PDF)

---

## Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/your-username/tracersmkn3.git
cd tracersmkn3
```

### 2. Instal Dependensi PHP & Node
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai kebutuhan:
```env
APP_NAME="Tracer Study SMKN 3 Pontianak"
APP_URL=http://localhost:8000

# Gunakan SQLite (default, tidak perlu konfigurasi tambahan):
DB_CONNECTION=sqlite

# Atau MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tracersmkn3
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Migrasi & Seeding Database
```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat:
- Data kompetensi/jurusan
- Data universitas & program studi
- Data 34 provinsi dan 514 kota/kabupaten Indonesia (dari CSV)
- Data negara
- Pengaturan awal sekolah
- Role dan akun pengguna awal (password default: `password`)

### 5. Build Assets Frontend
```bash
npm run build
```

### 6. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi dapat diakses di `http://localhost:8000`.

---

## Menjalankan untuk Development

Gunakan satu perintah untuk menjalankan server, queue, log, dan Vite secara bersamaan:

```bash
composer run dev
```

Atau gunakan `setup` untuk instalasi lengkap dari awal:
```bash
composer run setup
```

---

## Akun Default

Setelah seeding, akun berikut tersedia (password: `password`):

| Role | Email |
|---|---|
| Admin | `admin1@smkn3ptk.test` |
| Tata Usaha | `tu1@smkn3ptk.test` |
| Alumni | `alumni1@smkn3ptk.test` |

---

## Stack Teknologi

| Kategori | Teknologi |
|---|---|
| Framework | Laravel 12 |
| Frontend Reaktif | Livewire 4 |
| CSS | Tailwind CSS v4 |
| Autentikasi | Laravel UI + Laratrust (RBAC) |
| PDF | Spatie Laravel PDF (DOMPDF) |
| Excel | Maatwebsite Laravel Excel v3 |
| Database | SQLite / MySQL |
| Build Tool | Vite |

---

## Struktur Role

| Role | Akses |
|---|---|
| `admin` | Seluruh fitur admin |
| `tu` | Tata Usaha |
| `guru` | Guru |
| `waka` | Wakil Kepala Sekolah |
| `kepalasekolah` | Kepala Sekolah |
| `alumni` | Dashboard alumni & formulir tracer |
| `siswa` | Akun siswa |

---

## Lisensi

Aplikasi ini dikembangkan untuk keperluan internal SMKN 3 Pontianak.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
