
<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "db_mahasiswa";
$port = 3306;

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
