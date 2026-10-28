<html>
<head>
    <title>Daftar Buku Tamu</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 15px; }
        .pagination a { padding: 5px 10px; border: 1px solid #ddd; text-decoration: none; margin-right: 5px; color: black; }
        .pagination a.active { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>

    <h2>Daftar Buku Tamu</h2>
    <a href="form_tamu.php">+ Isi Buku Tamu</a><br><br>

    <?php
    $con = mysqli_connect("localhost", "root", "", "pemograman_web2");

    if (!$con) {
        die('Koneksi gagal: ' . mysqli_connect_error());
    }

    $batas = 5;
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

    $result_total = mysqli_query($con, "SELECT * FROM tbl_bukutamu");
    $jumlah_data = mysqli_num_rows($result_total);
    $total_halaman = ceil($jumlah_data / $batas);

    $hasil = mysqli_query($con, "SELECT * FROM tbl_bukutamu LIMIT $halaman_awal, $batas");
    $nomor = $halaman_awal + 1;
    ?>

    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Pesan</th>
            <th>Tanggal</th>
        </tr>
        <?php while ($data = mysqli_fetch_array($hasil)) { ?>
        <tr>
            <td><?php echo $nomor++; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['email']; ?></td>
            <td><?php echo $data['pesan']; ?></td>
            <td><?php echo $data['tanggal']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
            <a class="<?php echo ($halaman == $i) ? 'active' : ''; ?>" href="tampil_tamu.php?halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>
    </div>

    <?php mysqli_close($con); ?>

</body>
</html>