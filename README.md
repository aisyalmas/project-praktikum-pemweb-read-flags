# Read Flags

### A Book Review and Reading Tracker Website.

## Features

- **Landing Page** – Menampilkan halaman awal Read Flags yang berisi informasi aplikasi serta pilihan untuk Login dan Register.
- **Authentication** – Menyediakan fitur registrasi akun baru, login untuk mengakses aplikasi, serta logout untuk mengakhiri sesi pengguna.
- **Home** – Menampilkan ringkasan aktivitas membaca pengguna, meliputi Now Reading, Streak & Cadence, Challenge, Reading Task, dan On Your Shelves yang berisi kategori Up Next, Reading, dan Wishlist.
- **Library** – Menyediakan pengelolaan koleksi buku melalui beberapa tab, yaitu Books, Series, Author, Collection, dan Wishlist. Pengguna dapat mencari buku, mengatur status membaca, mengurutkan buku, serta mengubah tampilan Grid atau List.
- **Reviews** – Menampilkan daftar ulasan buku yang telah dibuat pengguna, dilengkapi fitur pencarian dan filter rating. Pengguna juga dapat menambahkan, mengubah, dan menghapus ulasan.
- **Profile** – Menampilkan informasi akun pengguna, menyediakan fitur edit profil, serta ringkasan aktivitas membaca seperti jumlah buku yang telah selesai dibaca, sedang dibaca, dan ingin dibaca.

## Tech Stack

**Frontend**

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

**Backend**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

**Database**

![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## Installation & Setup

Langkah-langkah menjalankan Read Flags secara lokal menggunakan XAMPP.

#### 1. Clone Repository

Unduh project dari GitHub menggunakan perintah berikut:

```bash
git clone https://github.com/aisyalmas/project-praktikum-pemweb-read-flags
```

Atau unduh repository melalui tombol **Code > Download ZIP** pada halaman GitHub.

#### 2. Move Project Folder

Pindahkan folder project ke dalam direktori `htdocs` pada XAMPP.

```text
C:\xampp\htdocs\read-flags
```

#### 3. Start Local Server

Buka XAMPP Control Panel, kemudian jalankan layanan berikut:

- Apache
- MySQL

#### 4. Setup Database

1. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
2. Buka tab **Import**.
3. Import ketiga file SQL yang tersedia pada repository sesuai urutan berikut:
   - `01_database.sql`
   - `02_data_buku.sql`
   - `03_dummy_user.sql`

4. Pastikan seluruh proses import berhasil dan tabel database telah terbentuk.

#### 5. Configure Project

Buka folder `config`, kemudian sesuaikan konfigurasi berikut:

- **Database Configuration** – Sesuaikan pengaturan koneksi database dengan konfigurasi MySQL lokal.
- **Base URL** – Ubah nilai `BASE_URL` sesuai dengan nama folder project di dalam direktori `htdocs`.


#### 6. Run Application

Setelah seluruh proses konfigurasi selesai, buka browser dan akses alamat berikut:

```text
http://localhost/read-flags/
```
Sesuaikan URL dengan nama folder project yang digunakan.

## Live Demo

Read Flags telah dideploy menggunakan layanan hosting dan dapat diakses secara online melalui link berikut:

**[Visit Read Flags](https://readflag.page.gd/)**

## Contributors

| Contributor | Contribution |
|---|---|
| Aisyah Almas Setyawan (123250093) | Perancangan UI/UX, Authentication, Fitur CRUD Review dan Profile |
| Rifani Juniarti (123250117) | Database MySQL, Navigation, Fitur CRUD Dashboard dan Library |


---
