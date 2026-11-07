# Laporan Singkat Pengujian
## Kartu Digital Mahasiswa — Modul 02

| No. | Skenario | Input | Hasil yang diharapkan |
|---|---|---|---|
| 1 | Nilai default | URL tanpa parameter | Budi Santoso, NIM 2211102001, angkatan 2022, IPK 3,75, Cum Laude |
| 2 | Input URL valid | `?nama=Ani&nim=2311102002&ipk=3.90` | Nama Ani, angkatan 2023, IPK 3,90, Cum Laude |
| 3 | Predikat sangat memuaskan | `?nama=Budi&nim=2211102001&ipk=3.20` | Predikat Sangat Memuaskan |
| 4 | IPK di bawah rentang | `?nama=Budi&nim=2211102001&ipk=2.40` | Predikat Perlu Perbaikan |
| 5 | Input tidak valid | `?nama=&nim=abc&ipk=8` | Pesan validasi muncul dan kartu tidak menampilkan data yang tidak valid |
| 6 | Uji keamanan nama | `?nama=%3Cscript%3Ealert(1)%3C%2Fscript%3E&nim=2211102001&ipk=3.75` | Teks input ditampilkan sebagai teks aman, bukan dijalankan sebagai skrip |

**Kesimpulan:** Program menerapkan input GET dengan nilai default, pengolahan variabel dan tipe data, perhitungan tahun angkatan, penentuan predikat IPK, pemformatan angka, serta pengamanan keluaran HTML. Ambil tangkapan layar hasil uji default dan parameter URL melalui browser untuk dilampirkan pada pengumpulan.
