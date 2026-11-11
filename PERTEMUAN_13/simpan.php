<?php
include "koneksi.php";

// Mengambil data dari form
$id_mahasiswa = trim($_POST['id_mahasiswa'] ?? '');
$nama         = trim($_POST['nama'] ?? '');
$jurusan      = trim($_POST['jurusan'] ?? '');
$alamat       = trim($_POST['alamat'] ?? '');
$telepon      = trim($_POST['telepon'] ?? '');

// Validasi data kosong
if (
    $id_mahasiswa === '' ||
    $nama === '' ||
    $jurusan === '' ||
    $alamat === '' ||
    $telepon === ''
) {
    echo "<script>
        alert('Semua data wajib diisi!');
        window.location='index.php';
    </script>";
    exit;
}

// Mengecek NIM di database
$cek = mysqli_prepare(
    $conn,
    "SELECT id_mahasiswa FROM mahasiswaa
     WHERE id_mahasiswa = ?"
);

if (!$cek) {
    die("Query pengecekan gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($cek, "s", $id_mahasiswa);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) > 0) {
    mysqli_stmt_close($cek);

    echo "<script>
        alert('NIM sudah digunakan!');
        window.location='index.php';
    </script>";
    exit;
}

mysqli_stmt_close($cek);

// Memasukkan data ke tabel mahasiswaa
$query = mysqli_prepare(
    $conn,
    "INSERT INTO mahasiswaa
    (id_mahasiswa, nama, jurusan, alamat, telepon)
    VALUES (?, ?, ?, ?, ?)"
);

if (!$query) {
    die("Query input gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $query,
    "sssss",
    $id_mahasiswa,
    $nama,
    $jurusan,
    $alamat,
    $telepon
);

// Menjalankan query
if (mysqli_stmt_execute($query)) {
    echo "<script>
        alert('Data mahasiswa berhasil disimpan!');
        window.location='data.php';
    </script>";
} else {
    echo "Data gagal disimpan: " .
         htmlspecialchars(mysqli_stmt_error($query));
}

mysqli_stmt_close($query);
mysqli_close($conn);
?>
