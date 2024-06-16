<?php
$s = "SELECT 1 FROM tb_sesi_kelas a 
JOIN tb_sesi b ON a.id_sesi=b.id
WHERE b.id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$num_rows = mysqli_num_rows($q);
if ($num_rows) {
  $inputs = div_alert('success', "Sudah ada $num_rows jadwal kelas pada room ini.<input type=hidden name=date_created value='$now'>");
} else {
  include 'aktivasi_room-status-7b.php';
}
