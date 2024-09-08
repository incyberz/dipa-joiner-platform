<?php
$id_paket = $_GET['id_paket'] ?? die(erid('id_paket'));
$s = "SELECT paket_kelas FROM tb_paket_kelas WHERE id_paket=$id_paket";
echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  $paket_kelas = $d['paket_kelas'];

  $s2 = "DELETE FROM tb_jawabans WHERE paket_kelas='$d[paket_kelas]'";
  echolog("-- $s");
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
}

$s = "DELETE FROM tb_paket_kelas WHERE id_paket=$id_paket";
echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

// $s = "DELETE FROM tb_jawabans WHERE id_paket=$id_paket";
// echolog($s);
// $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
// $s = "SELECT paket_kelas FROM tb_paket_kelas WHERE id_paket=$id_paket";
// echolog($s);
// $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
// while ($d = mysqli_fetch_assoc($q)) {
//   echo "<br>$d[paket_kelas]";
// }


$s = "DELETE FROM tb_paket WHERE id=$id_paket";
echolog($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
