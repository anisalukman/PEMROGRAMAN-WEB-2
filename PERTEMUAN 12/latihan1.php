<?php

$con = mysqli_connect("localhost", "root", "", "lat_dbase");

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sql = "UPDATE tbl_mhs SET Age = '36'
        WHERE FirstName = 'Karina'
        AND LastName = 'Suwandi'";

if (mysqli_query($con, $sql)) {
    echo "Data berhasil diperbarui!";
} else {
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);

?>
