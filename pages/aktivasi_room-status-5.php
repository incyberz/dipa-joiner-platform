<?php

$s = "SELECT 
a.id as id_sesi,
a.nama,
a.jenis,
a.no  
FROM tb_sesi a 
WHERE a.id_room=$id_room 
ORDER BY a.no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  die(div_alert('danger', "$Room ini belum memiliki sesi. Hubungi Developer jika ada kesalahan."));
} else {
  $tr = '';
  $no_sesi_normal = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $id_sesi = $d['id_sesi'];
    if ($d['jenis'] == 1) {
      $no_sesi_normal++;
      $no = $no_sesi_normal;
      $nama = "<input class='form-control' name=nama_sesi[$id_sesi] value='$d[nama]'>";
    } else {
      $nama = $d['nama'];
      $no = '&nbsp;';
    }
    $tr .= "
      <tr>
        <td>$no</td>
        <td>$nama</td>
      </tr>
    ";
  }
  $inputs = "
    <h3>Nama-nama Sesi untuk $Room $nama_room</h3>
    <p class='biru tebal'>Disarankan Anda mengisi nama-nama sesi berikut atau Anda boleh membiarkannya secara default.</p>
    <table class=table>
      <thead>
        <th width=60px>No</th>
        <th>Nama Sesi / Pertemuan</th>
      </thead>
      $tr
    </table>
  ";
}
