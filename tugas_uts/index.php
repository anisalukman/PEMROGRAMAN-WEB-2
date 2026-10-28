<?php
session_start();

/* =====================================================
   1. KONEKSI DATABASE
   ===================================================== */

$host = '127.0.0.1';
$port = '3306';
$dbname = 'calon_mhs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die(
        '<h3>Koneksi database gagal!</h3>' .
        '<p>Periksa port MySQL, nama database, username, dan password.</p>'
    );
}

/* =====================================================
   2. MEMBUAT TABEL JIKA BELUM ADA
   ===================================================== */

$sqlTabel = "
CREATE TABLE IF NOT EXISTS calon_mhs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    jenis_kelamin VARCHAR(20) NOT NULL,
    ttl VARCHAR(100) NOT NULL,
    asal_sekolah VARCHAR(150) NOT NULL,
    pekerjaan_ortu VARCHAR(100) DEFAULT NULL,
    matematika DECIMAL(5,2) NOT NULL,
    inggris DECIMAL(5,2) NOT NULL,
    umum DECIMAL(5,2) NOT NULL,
    rata_rata DECIMAL(5,2) NOT NULL,
    keterangan VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

try {
    $pdo->exec($sqlTabel);
} catch (PDOException $e) {
    die('Gagal memeriksa tabel database.');
}

/* =====================================================
   3. FUNGSI PENDUKUNG
   ===================================================== */

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$pesan = $_SESSION['pesan'] ?? '';
$jenisPesan = $_SESSION['jenis_pesan'] ?? '';

unset($_SESSION['pesan'], $_SESSION['jenis_pesan']);

/* =====================================================
   4. PROSES FORM
   ===================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Tombol batal */

    if (isset($_POST['batal'])) {
        header(
            'Location: ' . strtok($_SERVER['REQUEST_URI'], '?')
        );
        exit;
    }

    /* Tombol submit */

    if (isset($_POST['submit'])) {

        $nama = trim($_POST['nama'] ?? '');
        $jenisKelamin = $_POST['jenis_kelamin'] ?? '';
        $ttl = trim($_POST['ttl'] ?? '');
        $asalSekolah = trim($_POST['asal_sekolah'] ?? '');
        $pekerjaanOrtu = trim($_POST['pekerjaan_ortu'] ?? '');

        $matematika = $_POST['matematika'] ?? '';
        $inggris = $_POST['inggris'] ?? '';
        $umum = $_POST['umum'] ?? '';

        $tempatTes = $_POST['tempat_tes'] ?? 'A';
        $gelombang = $_POST['gelombang'] ?? '1';
        $bulanTes = $_POST['bulan_tes'] ?? '9';

        /* ---------------------------------------------
           VALIDASI DATA WAJIB
           --------------------------------------------- */

        if (
            $nama === '' ||
            $jenisKelamin === '' ||
            $ttl === '' ||
            $asalSekolah === '' ||
            $matematika === '' ||
            $inggris === '' ||
            $umum === ''
        ) {
            $_SESSION['pesan'] = 'Lengkapi data yang wajib diisi.';
            $_SESSION['jenis_pesan'] = 'error';

        } elseif (
            !in_array(
                $jenisKelamin,
                ['Laki-laki', 'Perempuan'],
                true
            ) ||
            !in_array($tempatTes, ['A', 'B', 'V'], true) ||
            !ctype_digit((string) $gelombang) ||
            (int) $gelombang < 1 ||
            (int) $gelombang > 9 ||
            !ctype_digit((string) $bulanTes) ||
            (int) $bulanTes < 1 ||
            (int) $bulanTes > 12
        ) {
            $_SESSION['pesan'] = 'Pilihan data tidak valid.';
            $_SESSION['jenis_pesan'] = 'error';

        } elseif (
            !is_numeric($matematika) ||
            !is_numeric($inggris) ||
            !is_numeric($umum) ||
            !is_finite((float) $matematika) ||
            !is_finite((float) $inggris) ||
            !is_finite((float) $umum) ||
            (float) $matematika < 0 ||
            (float) $matematika > 100 ||
            (float) $inggris < 0 ||
            (float) $inggris > 100 ||
            (float) $umum < 0 ||
            (float) $umum > 100
        ) {
            $_SESSION['pesan'] =
                'Nilai tes harus berupa angka 0 sampai 100.';
            $_SESSION['jenis_pesan'] = 'error';

        } else {

            try {

                /* -------------------------------------
                   HITUNG NILAI RATA-RATA
                   ------------------------------------- */

                $matematika = (float) $matematika;
                $inggris = (float) $inggris;
                $umum = (float) $umum;

                $rataRata = (
                    $matematika +
                    $inggris +
                    $umum
                ) / 3;

                $rataRata = round($rataRata, 2);

                /* -------------------------------------
                   TENTUKAN KETERANGAN KELULUSAN
                   ------------------------------------- */

                if ($rataRata >= 70) {
                    $keterangan = 'Lulus';
                } elseif ($rataRata >= 60) {
                    $keterangan = 'Cadangan';
                } else {
                    $keterangan = 'Tidak Lulus';
                }

                /* -------------------------------------
                   SIMPAN DATA KE DATABASE
                   ------------------------------------- */

                $pdo->beginTransaction();

                /*
                 * Simpan data terlebih dahulu dengan
                 * kode sementara yang unik.
                 *
                 * Setelah mendapatkan ID otomatis,
                 * kode pendaftaran diperbarui.
                 */

                $kodeSementara = 'TMP-' . bin2hex(random_bytes(12));

                $sqlInsert = "
                    INSERT INTO calon_mhs (
                        kode,
                        nama,
                        jenis_kelamin,
                        ttl,
                        asal_sekolah,
                        pekerjaan_ortu,
                        matematika,
                        inggris,
                        umum,
                        rata_rata,
                        keterangan
                    ) VALUES (
                        :kode,
                        :nama,
                        :jenis_kelamin,
                        :ttl,
                        :asal_sekolah,
                        :pekerjaan_ortu,
                        :matematika,
                        :inggris,
                        :umum,
                        :rata_rata,
                        :keterangan
                    )
                ";

                $stmt = $pdo->prepare($sqlInsert);

                $stmt->execute([
                    ':kode' => $kodeSementara,
                    ':nama' => $nama,
                    ':jenis_kelamin' => $jenisKelamin,
                    ':ttl' => $ttl,
                    ':asal_sekolah' => $asalSekolah,
                    ':pekerjaan_ortu' => $pekerjaanOrtu,
                    ':matematika' => $matematika,
                    ':inggris' => $inggris,
                    ':umum' => $umum,
                    ':rata_rata' => $rataRata,
                    ':keterangan' => $keterangan
                ]);

                /*
                 * ID diperoleh dari AUTO_INCREMENT.
                 * Nomor urut dimulai dari 101.
                 */

                $idBaru = (int) $pdo->lastInsertId();
                $nomorUrut = 100 + $idBaru;

                /* Membuat kode pendaftaran */

                $kode = $tempatTes
                    . $gelombang
                    . '-'
                    . $nomorUrut
                    . '-'
                    . $bulanTes;

                /* Memperbarui kode pendaftaran */

                $sqlUpdate = "
                    UPDATE calon_mhs
                    SET kode = :kode
                    WHERE id = :id
                ";

                $stmtUpdate = $pdo->prepare($sqlUpdate);

                $stmtUpdate->execute([
                    ':kode' => $kode,
                    ':id' => $idBaru
                ]);

                /* Konfirmasi penyimpanan */

                $pdo->commit();

                $_SESSION['pesan'] =
                    'Data pendaftar berhasil disimpan ke database.';

                $_SESSION['jenis_pesan'] = 'sukses';

            } catch (Throwable $e) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $_SESSION['pesan'] =
                    'Data gagal disimpan. Periksa struktur tabel database.';

                $_SESSION['jenis_pesan'] = 'error';
            }
        }

        /*
         * Redirect setelah submit agar refresh browser
         * tidak mengirim ulang data yang sama.
         */

        header(
            'Location: ' . strtok($_SERVER['REQUEST_URI'], '?')
        );
        exit;
    }
}

/* =====================================================
   5. AMBIL DATA DARI DATABASE
   ===================================================== */

try {

    $stmtData = $pdo->query("
        SELECT *
        FROM calon_mhs
        ORDER BY id ASC
    ");

    $dataPendaftar = $stmtData->fetchAll();

    $jumlahPendaftar = count($dataPendaftar);

    $jumlahLulus = 0;
    $jumlahCadangan = 0;
    $jumlahTidakLulus = 0;

    foreach ($dataPendaftar as $data) {

        if ($data['keterangan'] === 'Lulus') {
            $jumlahLulus++;

        } elseif ($data['keterangan'] === 'Cadangan') {
            $jumlahCadangan++;

        } elseif ($data['keterangan'] === 'Tidak Lulus') {
            $jumlahTidakLulus++;
        }
    }

    $kodeBerikutnya = 101;

    /*
     * Menggunakan AUTO_INCREMENT agar nomor urut
     * tidak kembali ke nomor lama setelah data dihapus.
     */

    $stmtAuto = $pdo->query("
        SELECT AUTO_INCREMENT
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'calon_mhs'
    ");

    $autoIncrement = $stmtAuto->fetchColumn();

    if ($autoIncrement !== false && $autoIncrement !== null) {
        $kodeBerikutnya = 100 + (int) $autoIncrement;
    }

} catch (PDOException $e) {
    die('Gagal mengambil data pendaftar dari database.');
}

/* =====================================================
   6. DAFTAR BULAN
   ===================================================== */

$bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Input Pendaftaran Mahasiswa Baru</title>

    <style>
        body {
            margin: 20px;
            color: #111;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .halaman {
            max-width: 1000px;
            margin: 0 auto;
        }

        h2 {
            font-size: 16px;
            margin: 0 0 12px;
        }

        .jumlah {
            margin-bottom: 10px;
        }

        .form-area {
            width: 430px;
            max-width: 100%;
        }

        .baris {
            display: grid;
            grid-template-columns: 125px 10px minmax(150px, 1fr);
            align-items: center;
            margin: 3px 0;
            min-height: 20px;
        }

        .baris label {
            font-size: 12px;
            font-weight: bold;
        }

        .titik {
            text-align: center;
        }

        input[type="text"],
        input[type="number"],
        select {
            box-sizing: border-box;
            width: 100%;
            height: 21px;
            border: 1px solid #888;
            padding: 1px 4px;
            border-radius: 0;
            font-size: 12px;
            background: #fff;
        }

        .radio-area {
            white-space: nowrap;
            font-size: 12px;
        }

        .radio-area input {
            vertical-align: middle;
        }

        .tombol {
            margin: 8px 0 12px 135px;
        }

        button {
            border: 1px solid #777;
            border-radius: 0;
            background: #eee;
            padding: 3px 12px;
            font-size: 12px;
            cursor: pointer;
        }

        .pesan {
            margin: 8px 0;
            padding: 8px;
        }

        .sukses {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .tabel-title {
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 800px;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 3px 4px;
            text-align: center;
        }

        th {
            font-weight: bold;
            white-space: nowrap;
        }

        td.kiri {
            text-align: left;
        }

        .rekap td {
            text-align: right;
            font-weight: bold;
        }

        .keterangan-soal {
            display: flex;
            gap: 20px;
            margin-top: 12px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .kotak {
            border: 1px solid #777;
            padding: 8px;
            font-size: 14px;
            line-height: 1.3;
        }

        .kotak-kode {
            width: 270px;
        }

        .kotak-kode p {
            margin: 3px 0;
        }

        .kotak-kode ul {
            margin: 3px 0 8px;
            padding-left: 20px;
        }

        .kotak-ket {
            width: 400px;
        }

        @media (max-width: 600px) {

            body {
                margin: 10px;
            }

            .baris {
                grid-template-columns: 115px 8px minmax(130px, 1fr);
            }

            .tombol {
                margin-left: 123px;
            }

            .kotak-kode,
            .kotak-ket {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<div class="halaman">

    <h2>INPUT PENDAFTARAN</h2>

    <div class="jumlah">
        <strong>JUMLAH DATA</strong>
        &nbsp; : [ <?= $jumlahPendaftar ?> ]
    </div>

    <!-- =========================================
         INPUT DATA PENDAFTAR
         ========================================= -->

    <form method="post" action="">

        <div class="form-area">

            <div class="baris">

                <label for="tempat_tes">
                    KODE PENDAFTARAN
                </label>

                <span class="titik">:</span>

                <div style="display:flex; gap:4px;">

                    <select
                        id="tempat_tes"
                        name="tempat_tes"
                        aria-label="Tempat tes"
                    >
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="V">V</option>
                    </select>

                    <select
                        name="gelombang"
                        aria-label="Gelombang"
                    >
                        <?php for ($i = 1; $i <= 9; $i++): ?>

                            <option value="<?= $i ?>">
                                <?= $i ?>
                            </option>

                        <?php endfor; ?>
                    </select>

                    <span style="align-self:center;">
                        -<?= $kodeBerikutnya ?>-
                    </span>

                    <select
                        name="bulan_tes"
                        aria-label="Bulan tes"
                    >
                        <?php foreach ($bulan as $angka => $namaBulan): ?>

                            <option
                                value="<?= $angka ?>"
                                <?= $angka === 9 ? 'selected' : '' ?>
                            >
                                <?= $angka ?>
                            </option>

                        <?php endforeach; ?>
                    </select>

                </div>

            </div>

            <div class="baris">

                <label for="nama">NAMA PENDAFTAR</label>

                <span class="titik">:</span>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    maxlength="150"
                    required
                >

            </div>

            <div class="baris">

                <label>JENIS KELAMIN</label>

                <span class="titik">:</span>

                <div class="radio-area">

                    <label>
                        <input
                            type="radio"
                            name="jenis_kelamin"
                            value="Laki-laki"
                            required
                        >
                        Laki-laki
                    </label>

                    &nbsp;

                    <label>
                        <input
                            type="radio"
                            name="jenis_kelamin"
                            value="Perempuan"
                        >
                        Perempuan
                    </label>

                </div>

            </div>

            <div class="baris">

                <label for="ttl">TTL</label>

                <span class="titik">:</span>

                <input
                    type="text"
                    id="ttl"
                    name="ttl"
                    maxlength="100"
                    placeholder="Contoh: Depok, 10 Januari 2008"
                    required
                >

            </div>

            <div class="baris">

                <label for="asal_sekolah">ASAL SEKOLAH</label>

                <span class="titik">:</span>

                <input
                    type="text"
                    id="asal_sekolah"
                    name="asal_sekolah"
                    maxlength="150"
                    required
                >

            </div>

            <div class="baris">

                <label for="pekerjaan_ortu">
                    PEKERJAAN ORTU
                </label>

                <span class="titik">:</span>

                <input
                    type="text"
                    id="pekerjaan_ortu"
                    name="pekerjaan_ortu"
                    maxlength="100"
                >

            </div>

            <div class="baris">

                <label for="matematika">
                    NILAI TES MATEMATIKA
                </label>

                <span class="titik">:</span>

                <input
                    type="number"
                    id="matematika"
                    name="matematika"
                    min="0"
                    max="100"
                    step="any"
                    required
                >

            </div>

            <div class="baris">

                <label for="inggris">
                    BAHASA INGGRIS
                </label>

                <span class="titik">:</span>

                <input
                    type="number"
                    id="inggris"
                    name="inggris"
                    min="0"
                    max="100"
                    step="any"
                    required
                >

            </div>

            <div class="baris">

                <label for="umum">UMUM</label>

                <span class="titik">:</span>

                <input
                    type="number"
                    id="umum"
                    name="umum"
                    min="0"
                    max="100"
                    step="any"
                    required
                >

            </div>

        </div>

        <div class="tombol">

            <button type="submit" name="submit">
                SUBMIT
            </button>

            <button
                type="submit"
                name="batal"
                formnovalidate
            >
                BATAL
            </button>

        </div>

    </form>

    <!-- =========================================
         PESAN HASIL PROSES
         ========================================= -->

    <?php if ($pesan !== ''): ?>

        <p class="pesan <?= e($jenisPesan) ?>">
            <?= e($pesan) ?>
        </p>

    <?php endif; ?>

    <!-- =========================================
         OUTPUT TABEL DATA
         ========================================= -->

    <div class="tabel-title">
        TABEL DATA
    </div>

    <div class="table-wrap">

        <table>

            <thead>

                <tr>
                    <th>No.</th>
                    <th>Kode</th>
                    <th>Kode Pendf.</th>
                    <th>Nama Pendft.</th>
                    <th>Jns Kel</th>
                    <th>TTL</th>
                    <th>Asal Sekolah</th>
                    <th>Pek. Ortu</th>
                    <th>Mat</th>
                    <th>B. Ingr</th>
                    <th>Umum</th>
                    <th>Rata-Rata</th>
                    <th>Keterangan</th>
                </tr>

            </thead>

            <tbody>

                <?php if (empty($dataPendaftar)): ?>

                    <tr>
                        <td colspan="13">
                            Belum ada data pendaftar.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($dataPendaftar as $i => $p): ?>

                        <tr>

                            <td>
                                <?= $i + 1 ?>
                            </td>

                            <td>
                                <?= e(substr($p['kode'], 0, 2)) ?>
                            </td>

                            <td>
                                <?= e($p['kode']) ?>
                            </td>

                            <td class="kiri">
                                <?= e($p['nama']) ?>
                            </td>

                            <td>
                                <?= e($p['jenis_kelamin']) ?>
                            </td>

                            <td>
                                <?= e($p['ttl']) ?>
                            </td>

                            <td class="kiri">
                                <?= e($p['asal_sekolah']) ?>
                            </td>

                            <td class="kiri">
                                <?= e($p['pekerjaan_ortu']) ?>
                            </td>

                            <td>
                                <?= e($p['matematika']) ?>
                            </td>

                            <td>
                                <?= e($p['inggris']) ?>
                            </td>

                            <td>
                                <?= e($p['umum']) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (float) $p['rata_rata'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= e($p['keterangan']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                <tr class="rekap">

                    <td colspan="12">
                        JUMLAH PENDAFTAR
                    </td>

                    <td>
                        <?= $jumlahPendaftar ?>
                    </td>

                </tr>

                <tr class="rekap">

                    <td colspan="12">
                        JUMLAH PESERTA LULUS
                    </td>

                    <td>
                        <?= $jumlahLulus ?>
                    </td>

                </tr>

                <tr class="rekap">

                    <td colspan="12">
                        JUMLAH PESERTA CADANGAN
                    </td>

                    <td>
                        <?= $jumlahCadangan ?>
                    </td>

                </tr>

                <tr class="rekap">

                    <td colspan="12">
                        JUMLAH PESERTA TIDAK LULUS
                    </td>

                    <td>
                        <?= $jumlahTidakLulus ?>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <!-- =========================================
         KETERANGAN KODE PENDAFTARAN
         ========================================= -->

    <div class="keterangan-soal">

        <div class="kotak kotak-kode">

            <strong>Kode pendaftaran:</strong>

            <ul>

                <li>
                    Dua karakter awal menunjukkan
                    tempat tes dan gelombang.

                    <br>A = Gedung A
                    <br>B = Gedung B
                    <br>V = Viktor
                </li>

                <li>
                    Angka setelah tanda hubung pertama
                    adalah nomor urut pendaftaran.
                </li>

                <li>
                    Angka terakhir menunjukkan
                    bulan tes.
                </li>

            </ul>

            <p>Contoh: A2</p>
            <p>A2-101-9</p>
            <p>Ket-tes: Gedung A – Gelombang 2</p>
            <p>Bln Tes: September</p>

        </div>

        <div class="kotak kotak-ket">

            <ul>

                <li>
                    RATA-RATA = (MAT + B. INGGRIS + UMUM) / 3
                </li>

                <li>
                    Jika rata-rata minimal 70,
                    keterangan Lulus.
                </li>

                <li>
                    Jika rata-rata minimal 60 dan kurang dari 70,
                    keterangan Cadangan.
                </li>

                <li>
                    Jika rata-rata kurang dari 60,
                    keterangan Tidak Lulus.
                </li>

            </ul>

        </div>

    </div>

</div>

</body>

</html>
