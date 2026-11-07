<?php
declare(strict_types=1);

/*
 * Kartu Digital Mahasiswa
 * Praktik Modul 02 - Dasar PHP
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Konstanta program
const NAMA_KAMPUS = 'STT Mandala';
const KODE_PRODI = 'IF';
const NAMA_PRODI = 'Teknik Informatika';

// Membaca input GET dengan nilai default dan memastikan nilainya skalar
$namaInput = $_GET['nama'] ?? 'Budi Santoso';
$nimInput = $_GET['nim'] ?? '2211102001';
$ipkInput = $_GET['ipk'] ?? '3.75';

$nama = is_string($namaInput) ? trim($namaInput) : 'Budi Santoso';
$nim = is_string($nimInput) ? trim($nimInput) : '2211102001';
$ipkMentah = is_string($ipkInput) ? $ipkInput : '3.75';

// Casting eksplisit sesuai materi modul
$ipk = (float) $ipkMentah;

// Validasi sederhana input
$pesanError = [];
if ($nama === '') {
    $pesanError[] = 'Nama tidak boleh kosong.';
}
if (!preg_match('/^\d{2,}$/', $nim)) {
    $pesanError[] = 'NIM harus berupa angka dan minimal 2 digit.';
}
if (!is_numeric($ipkMentah) || $ipk < 0 || $ipk > 4) {
    $pesanError[] = 'IPK harus berupa angka antara 0,00 sampai 4,00.';
}

// Menghitung tahun angkatan dari dua digit pertama NIM
$tahunAngkatan = 0;
if (preg_match('/^\d{2,}$/', $nim)) {
    $tahunAngkatan = 2000 + (int) substr($nim, 0, 2);
}

// Menentukan predikat IPK menggunakan ternary bertingkat
$predikat = $ipk >= 3.5 ? 'Cum Laude'
    : ($ipk >= 3.0 ? 'Sangat Memuaskan'
    : ($ipk >= 2.5 ? 'Memuaskan' : 'Perlu Perbaikan'));

// Memformat IPK dan mengamankan keluaran HTML
$ipkTampil = number_format($ipk, 2, ',', '.');
$namaAman = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
$nimAman = htmlspecialchars($nim, ENT_QUOTES, 'UTF-8');
$kampusAman = htmlspecialchars(NAMA_KAMPUS, ENT_QUOTES, 'UTF-8');
$prodiAman = htmlspecialchars(NAMA_PRODI, ENT_QUOTES, 'UTF-8');
$predikatAman = htmlspecialchars($predikat, ENT_QUOTES, 'UTF-8');
$metodeRequest = htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'CLI', ENT_QUOTES, 'UTF-8');

// Data contoh untuk ditampilkan pada form
$namaForm = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
$nimForm = htmlspecialchars($nim, ENT_QUOTES, 'UTF-8');
$ipkForm = htmlspecialchars($ipkMentah, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Digital Mahasiswa | STT Mandala</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; padding: 36px 18px;
            font-family: Arial, Helvetica, sans-serif;
            color: #172033; background: #f1f5f9;
        }
        .container { width: min(960px, 100%); margin: 0 auto; }
        .heading { margin-bottom: 24px; }
        .eyebrow { color: #2563eb; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        h1 { margin: 8px 0; font-size: clamp(26px, 4vw, 36px); }
        .subtitle { margin: 0; color: #64748b; line-height: 1.6; }
        .layout { display: grid; grid-template-columns: 1fr 1.1fr; gap: 22px; align-items: start; }
        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: 0 12px 30px rgba(15,23,42,.05); }
        h2 { margin: 0 0 18px; font-size: 18px; }
        label { display: block; margin: 14px 0 7px; font-size: 13px; font-weight: 700; color: #334155; }
        input { width: 100%; border: 1px solid #cbd5e1; border-radius: 9px; padding: 12px 13px; font-size: 14px; outline: none; }
        input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
        button { width: 100%; margin-top: 20px; padding: 13px; border: 0; border-radius: 9px; background: #1d4ed8; color: white; font-weight: 700; font-size: 14px; cursor: pointer; }
        button:hover { background: #1e40af; }
        .hint { color: #64748b; font-size: 12px; line-height: 1.5; margin: 12px 0 0; }
        .error { margin-bottom: 18px; padding: 12px 14px; border-radius: 10px; color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; font-size: 13px; }
        .error ul { margin: 7px 0 0; padding-left: 18px; }
        .student-card { overflow: hidden; border-radius: 18px; background: linear-gradient(135deg, #0f172a, #1e40af); color: white; }
        .card-top { padding: 24px 25px 20px; border-bottom: 1px solid rgba(255,255,255,.18); }
        .campus { font-size: 12px; letter-spacing: 1.4px; color: #bfdbfe; text-transform: uppercase; }
        .card-title { margin: 10px 0 0; font-size: 21px; }
        .card-body { padding: 25px; }
        .student-name { font-size: 25px; font-weight: 700; margin: 0 0 5px; overflow-wrap: anywhere; }
        .nim { color: #bfdbfe; font-size: 13px; letter-spacing: 1px; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 12px; margin-top: 26px; }
        .detail-label { color: #bfdbfe; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .detail-value { margin-top: 6px; font-size: 15px; font-weight: 700; overflow-wrap: anywhere; }
        .grade { display: inline-block; margin-top: 22px; padding: 9px 13px; border-radius: 30px; background: #dbeafe; color: #1e40af; font-size: 12px; font-weight: 700; }
        .card-foot { padding: 14px 25px; background: rgba(2,6,23,.25); color: #cbd5e1; font-size: 11px; }
        .note { margin-top: 18px; color: #64748b; font-size: 12px; line-height: 1.6; }
        .request { margin-top: 14px; color: #94a3b8; font-size: 11px; text-align: right; }
        @media (max-width: 720px) { .layout { grid-template-columns: 1fr; } body { padding: 24px 14px; } }
    </style>
</head>
<body>
<div class="container">
    <header class="heading">
        <div class="eyebrow">Praktik Pemrograman Web · Modul 02</div>
        <h1>Kartu Digital Mahasiswa</h1>
        <p class="subtitle">Contoh penerapan variabel, tipe data, operator, konstanta, serta input dan output PHP.</p>
    </header>

    <main class="layout">
        <section class="panel">
            <h2>Input Data Mahasiswa</h2>
            <?php if ($pesanError !== []): ?>
                <div class="error">
                    <strong>Periksa kembali data yang dimasukkan:</strong>
                    <ul>
                        <?php foreach ($pesanError as $pesan): ?>
                            <li><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="GET" action="">
                <label for="nama">Nama lengkap</label>
                <input id="nama" name="nama" type="text" value="<?= $namaForm ?>" placeholder="Contoh: Budi Santoso">
                <label for="nim">NIM</label>
                <input id="nim" name="nim" type="text" inputmode="numeric" value="<?= $nimForm ?>" placeholder="Contoh: 2211102001">
                <label for="ipk">IPK</label>
                <input id="ipk" name="ipk" type="number" min="0" max="4" step="0.01" value="<?= $ipkForm ?>" placeholder="Contoh: 3.75">
                <button type="submit">Tampilkan Kartu</button>
            </form>
            <p class="hint">Form menggunakan metode GET. Data juga dapat diubah langsung melalui parameter URL.</p>
        </section>

        <section>
            <div class="student-card">
                <div class="card-top">
                    <div class="campus"><?= $kampusAman ?> · <?= KODE_PRODI ?></div>
                    <div class="card-title">KARTU DIGITAL MAHASISWA</div>
                </div>
                <div class="card-body">
                    <?php if ($pesanError === []): ?>
                        <p class="student-name"><?= $namaAman ?></p>
                        <div class="nim">NIM · <?= $nimAman ?></div>
                        <div class="details">
                            <div>
                                <div class="detail-label">Program Studi</div>
                                <div class="detail-value"><?= $prodiAman ?></div>
                            </div>
                            <div>
                                <div class="detail-label">Tahun Angkatan</div>
                                <div class="detail-value"><?= $tahunAngkatan ?></div>
                            </div>
                            <div>
                                <div class="detail-label">Indeks Prestasi Kumulatif</div>
                                <div class="detail-value"><?= $ipkTampil ?></div>
                            </div>
                            <div>
                                <div class="detail-label">Kategori Angkatan</div>
                                <div class="detail-value">Angkatan <?= $tahunAngkatan ?></div>
                            </div>
                        </div>
                        <span class="grade"><?= $predikatAman ?></span>
                    <?php else: ?>
                        <p class="student-name">Data belum valid</p>
                        <div class="nim">Silakan perbaiki input pada formulir.</div>
                    <?php endif; ?>
                </div>
                <div class="card-foot">Kartu digital untuk simulasi pembelajaran PHP · <?= $kampusAman ?></div>
            </div>
            <div class="request">Metode request: <?= $metodeRequest ?></div>
            <p class="note">Catatan: tahun angkatan dihitung dari dua digit pertama NIM, sesuai asumsi pada Modul 02. Predikat IPK mengikuti rentang yang dicontohkan dalam modul.</p>
        </section>
    </main>
</div>
</body>
</html>
