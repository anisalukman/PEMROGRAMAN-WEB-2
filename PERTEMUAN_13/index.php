
<!DOCTYPE html>
<html>
<head>
    <title>Form Input Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

    <form action="simpan.php" method="POST">

        <table>
            <tr height="46">
                <td width="25%"></td>
                <td colspan="2" class="judul">
                    Form Input Data Mahasiswa
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td>ID Mahasiswa / NIM</td>
                <td>
                    <input type="text"
                    name="id_mahasiswa"
                    class="nim"
                    maxlength="20"
                    required>
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td>Nama</td>
                <td>
                    <input type="text"
                    name="nama"
                    class="nama"
                    maxlength="100"
                    required>
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td>Jurusan</td>
                <td>
                    <select name="jurusan" required>
                        <option value="">- Pilih Jurusan -</option>
                        <option value="Teknik Komputer">
                            Teknik Komputer
                        </option>
                        <option value="Teknik Informatika">
                            Teknik Informatika
                        </option>
                        <option value="Teknik Mesin">
                            Teknik Mesin
                        </option>
                        <option value="Teknik Elektro">
                            Teknik Elektro
                        </option>
                        <option value="Komputer Akuntansi">
                            Komputer Akuntansi
                        </option>
                    </select>
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td>Alamat</td>
                <td>
                    <input type="text"
                    name="alamat"
                    class="alamat"
                    maxlength="255"
                    required>
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td>No. Telp</td>
                <td>
                    <input type="text"
                    name="telepon"
                    class="telepon"
                    maxlength="20"
                    required>
                </td>
            </tr>

            <tr height="46">
                <td></td>
                <td></td>
                <td>
                    <input type="submit"
                    name="Submit"
                    value="Submit">

                    <input type="reset"
                    name="reset"
                    value="Cancel">
                </td>
            </tr>

        </table>

    </form>

</div>

</body>
</html>
