<?php
declare(strict_types=1);

/**
 * Tugas CBL Modul 03 — Laporan Nilai Kelas dan Peringkat
 * Jalankan: php -S localhost:8000  lalu buka http://localhost:8000
 */

// ---------- 1. Ambil & parse data (format: Nama=Nilai, satu per baris) ----------
$contoh = "Andi=88\nBudi=72\nCitra=45\nDewi=91\nEka=65\nFajar=50\nGita=85\nHadi=75";
$input = $_POST['data'] ?? $contoh;
if (!is_string($input)) {
    $input = $contoh;
}

$mahasiswa = [];
$galat = [];
foreach (preg_split('/\R/', trim($input)) as $no => $baris) {
    $baris = trim($baris);
    if ($baris === '') {
        continue; // lewati baris kosong
    }
    $bagian = explode('=', $baris, 2);
    $nama = trim($bagian[0]);
    $nilai = trim($bagian[1] ?? '');
    // validasi: nama wajib ada, nilai numerik 0-100
    if ($nama === '' || !is_numeric($nilai) || (float) $nilai < 0 || (float) $nilai > 100) {
        $galat[] = 'Baris ' . ($no + 1) . ' tidak valid: "' . $baris . '"';
        continue;
    }
    $mahasiswa[$nama] = (float) $nilai;
}

// ---------- 2. Huruf mutu, status, catatan + statistik (satu foreach) ----------
$laporan = [];
$total = 0.0;
$maks = 0.0;
$min = 100.0;
$lulus = 0;

foreach ($mahasiswa as $nama => $akhir) {
    // Rantai if-elseif-else: ambang diurutkan dari tertinggi
    if ($akhir >= 85) {
        $huruf = 'A';
    } elseif ($akhir >= 75) {
        $huruf = 'B';
    } elseif ($akhir >= 65) {
        $huruf = 'C';
    } elseif ($akhir >= 50) {
        $huruf = 'D';
    } else {
        $huruf = 'E';
    }

    // match: predikat huruf mutu (perbandingan ketat)
    $predikat = match ($huruf) {
        'A' => 'Istimewa',
        'B' => 'Sangat Baik',
        'C' => 'Baik',
        'D' => 'Cukup',
        default => 'Kurang',
    };

    // Status kelulusan sesuai tabel aturan modul
    if ($akhir >= 65) {
        $status = 'Lulus';
    } elseif ($akhir >= 50) {
        $status = 'Lulus Bersyarat';
    } else {
        $status = 'Tidak Lulus';
    }

    // Catatan khusus untuk nilai tepat di ambang batas
    $catatan = '';
    if ($akhir === 50.0) {
        $catatan = 'Tepat di ambang kelulusan (50)';
    }

    $laporan[$nama] = compact('akhir', 'huruf', 'predikat', 'status', 'catatan');

    // Akumulasi statistik
    $total += $akhir;
    $maks = max($maks, $akhir);
    $min = min($min, $akhir);
    if ($akhir >= 50) {
        $lulus++;
    }
}

// Tangani array kosong agar tidak terjadi pembagian dengan nol
$jumlah = count($mahasiswa);
$rata = $jumlah > 0 ? $total / $jumlah : 0.0;

// ---------- 3. Peringkat (arsort, nilai sama = peringkat sama) ----------
$urut = $mahasiswa;
arsort($urut);
$peringkat = [];
$posisi = 0;
$rankSaatIni = 0;
$sebelumnya = null;
foreach ($urut as $nama => $akhir) {
    $posisi++;
    if ($akhir !== $sebelumnya) {
        $rankSaatIni = $posisi;
    }
    $sebelumnya = $akhir;
    $peringkat[] = ['rank' => $rankSaatIni, 'nama' => $nama, 'akhir' => $akhir];
}

// Tampilkan nilai tanpa desimal bila bulat
$fmt = static fn (float $v): string => number_format($v, $v === floor($v) ? 0 : 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laporan Nilai Kelas dan Peringkat</title>
<style>
:root{--bg:#f6f7fb;--card:#fff;--tx:#1b1f2a;--mut:#667085;--bd:#e3e6ee;--ac:#4f46e5;--ok:#15803d;--wr:#b45309;--er:#b91c1c}
@media (prefers-color-scheme:dark){:root{--bg:#12141b;--card:#1b1e28;--tx:#eceef4;--mut:#9aa1b2;--bd:#2c3140;--ac:#818cf8;--ok:#4ade80;--wr:#fbbf24;--er:#f87171}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--tx);font:16px/1.5 system-ui,sans-serif}
main{max-width:960px;margin:0 auto;padding:24px 16px}
h1{margin:0 0 4px;font-size:1.6rem}
.sub{color:var(--mut);margin:0 0 20px}
.card{background:var(--card);border:1px solid var(--bd);border-radius:12px;padding:16px;margin-bottom:16px}
h2{margin:0 0 12px;font-size:1.1rem}
textarea{width:100%;min-height:150px;font:14px/1.5 ui-monospace,monospace;padding:10px;border:1px solid var(--bd);border-radius:8px;background:var(--bg);color:var(--tx)}
button{margin-top:10px;padding:9px 18px;border:0;border-radius:8px;background:var(--ac);color:#fff;font-weight:600;cursor:pointer}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px}
.stat{border:1px solid var(--bd);border-radius:10px;padding:10px}
.stat b{display:block;font-size:1.4rem}
.stat span{color:var(--mut);font-size:.85rem}
.scroll{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.95rem}
th,td{text-align:left;padding:8px 10px;border-bottom:1px solid var(--bd);white-space:nowrap}
th{color:var(--mut);font-weight:600}
.Lulus{color:var(--ok)}.Bersyarat{color:var(--wr)}.Tidak{color:var(--er)}
.err{color:var(--er);margin:4px 0}
.hint{color:var(--mut);font-size:.85rem}
</style>
</head>
<body>
<main>
  <h1>Laporan Nilai Kelas dan Peringkat</h1>
  <p class="sub">Modul 03 — Struktur Kontrol PHP (if-elseif, match, foreach)</p>

  <form method="post" class="card">
    <h2>Data Nilai</h2>
    <textarea name="data" aria-label="Data nilai"><?= htmlspecialchars($input, ENT_QUOTES, 'UTF-8') ?></textarea>
    <p class="hint">Satu mahasiswa per baris, format <code>Nama=Nilai</code> (0–100).</p>
    <button type="submit">Buat Laporan</button>
  </form>

  <?php foreach ($galat as $pesan): ?>
    <p class="err"><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endforeach; ?>

  <?php if ($jumlah === 0): ?>
    <div class="card"><p>Belum ada data valid. Masukkan minimal satu baris <code>Nama=Nilai</code>.</p></div>
  <?php else: ?>
    <div class="card">
      <h2>Ringkasan Kelas</h2>
      <div class="stats">
        <div class="stat"><b><?= number_format($rata, 2) ?></b><span>Rata-rata</span></div>
        <div class="stat"><b><?= $fmt($maks) ?></b><span>Tertinggi</span></div>
        <div class="stat"><b><?= $fmt($min) ?></b><span>Terendah</span></div>
        <div class="stat"><b><?= $lulus ?> / <?= $jumlah ?></b><span>Mahasiswa lulus</span></div>
      </div>
    </div>

    <div class="card">
      <h2>Rekap Nilai</h2>
      <div class="scroll">
        <table>
          <tr><th>Nama</th><th>Nilai</th><th>Huruf</th><th>Predikat</th><th>Status</th><th>Catatan</th></tr>
          <?php foreach ($laporan as $nama => $r): ?>
            <?php
            if ($r['status'] === 'Lulus') {
                $kelas = 'Lulus';
            } elseif ($r['status'] === 'Lulus Bersyarat') {
                $kelas = 'Bersyarat';
            } else {
                $kelas = 'Tidak';
            }
            ?>
            <tr>
              <td><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= $fmt($r['akhir']) ?></td>
              <td><?= $r['huruf'] ?></td>
              <td><?= $r['predikat'] ?></td>
              <td class="<?= $kelas ?>"><?= $r['status'] ?></td>
              <td><?= htmlspecialchars($r['catatan'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>

    <div class="card">
      <h2>Peringkat</h2>
      <div class="scroll">
        <table>
          <tr><th>Peringkat</th><th>Nama</th><th>Nilai</th></tr>
          <?php foreach ($peringkat as $p): ?>
            <tr>
              <td>#<?= $p['rank'] ?></td>
              <td><?= htmlspecialchars($p['nama'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= $fmt($p['akhir']) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
