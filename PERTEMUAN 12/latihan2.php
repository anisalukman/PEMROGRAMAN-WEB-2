
<?php

$con = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "DELETE FROM tbl_mhs WHERE LastName = 'Prabowo'";

if (mysqli_query($con, $query)) {
    echo "Data berhasil dihapus!";
} else {
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);

?>
