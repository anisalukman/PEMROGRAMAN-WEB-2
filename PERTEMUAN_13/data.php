
<?php
include "koneksi.php";

$query = mysqli_query(
    $conn,
    "SELECT * FROM mahasiswaa ORDER BY id_mahasiswa ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container wide">
    <h2>Data Mahasiswa</h2>

    <a href="index.php" class="link">
        + Tambah Mahasiswa
    </a>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Alamat</th>
                <th>Telepon</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['id_mahasiswa']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['nama']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['jurusan']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['alamat']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['telepon']) ?>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
