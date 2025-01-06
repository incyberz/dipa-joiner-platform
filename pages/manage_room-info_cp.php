<?php
$s = "SELECT * FROM tb_cp a WHERE a.id_room=$id_room ORDER BY kelas,semester,no_cat";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $tr .= "
    <tr id=tr__$d[id]>
      <td>$i</td>
      <td>$d[kelas]</td>
      <td>$d[semester]</td>
      <td>$d[kategori_keterampilan]</td>
      <td>$d[deskripsi_cp]</td>
    </tr>
  ";
}

$info_cp = "
  <div class='wadah gradasi-toska'>
    <h3>Info Capaian Pembelajaran</h3>
    <table class='table'>
      <thead class='gradasi-kuning'>
        <th>No</th>
        <th>Kelas</th>
        <th>Semester</th>
        <th>Kategori Keterampilan</th>
        <th>Deskripsi CP</th>
      </thead>
      $tr
    </table>
  </div>
";
