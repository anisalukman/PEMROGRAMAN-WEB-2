<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

$nama = $_POST['nama'];
$email = $_POST['email'];
$pesan = $_POST['pesan'];

$sql = "INSERT INTO tbl_bukutamu (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";

if (mysqli_query($con, $sql)) {
    echo "Pesan berhasil disimpan! <br><a href='form_tamu.php'>Kembali</a> | <a href='tampil_tamu.php'>Lihat Buku Tamu</a>";
} else {
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);
?>