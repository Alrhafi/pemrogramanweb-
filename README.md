# Kartu Digital Mahasiswa — Modul 02 Dasar PHP

## Deskripsi
Web sederhana untuk tugas praktik Modul 02. Data mahasiswa dibaca dari `$_GET`, kemudian ditampilkan sebagai kartu digital.

## Ketentuan modul yang diterapkan
- `declare(strict_types=1);`
- Variabel dan konstanta (`NAMA_KAMPUS`, `KODE_PRODI`, `NAMA_PRODI`)
- Tipe data dan casting eksplisit `(float)` serta `(int)`
- Operator ternary untuk menentukan predikat IPK
- `$_GET` dengan nilai default menggunakan `??`
- `substr()` untuk mengambil dua digit pertama NIM
- `number_format()` untuk format IPK dua desimal
- `htmlspecialchars()` untuk mengamankan keluaran HTML dari XSS
- Komentar kode dan struktur indentasi yang rapi

## Cara menjalankan dengan XAMPP
1. Ekstrak folder `kartu-digital-mahasiswa` ke `C:\xampp\htdocs\`.
2. Jalankan Apache melalui XAMPP Control Panel.
3. Buka `http://localhost/kartu-digital-mahasiswa/`.

## Cara menjalankan dengan PHP built-in server
Buka terminal pada folder project, lalu jalankan:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/`.

## Uji parameter URL
Nilai default:
`http://localhost:8000/`

Skenario dengan parameter:
`http://localhost:8000/?nama=Ani&nim=2311102002&ipk=3.90`

Skenario input tidak valid:
`http://localhost:8000/?nama=&nim=abc&ipk=8`

## Uji CLI
Dari folder project:
```bash
php index.php
```
Saat dijalankan melalui CLI, `$_GET` kosong sehingga nilai default digunakan. Tampilan HTML dapat dilihat melalui browser.

## Predikat IPK
- IPK >= 3,50: Cum Laude
- IPK >= 3,00: Sangat Memuaskan
- IPK >= 2,50: Memuaskan
- Di bawah 2,50: Perlu Perbaikan

## Catatan
Program ini menggunakan asumsi pada modul bahwa dua digit pertama NIM menunjukkan tahun angkatan (misalnya `22` menjadi 2022). Program studi dan kampus menggunakan contoh STT Mandala pada modul.
