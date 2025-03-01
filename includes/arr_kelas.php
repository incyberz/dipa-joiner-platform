<?php
if (isset($cn)) {
  $s = "SELECT a.kelas,
  (
    SELECT count(1) 
    FROM tb_peserta WHERE kelas=a.kelas) jumlah_peserta 
  FROM tb_kelas a 
  WHERE a.ta = $ta_aktif
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $arr_kelas = [];
  while ($d = mysqli_fetch_assoc($q)) {
    $arr_kelas[$d['kelas']] = $d['jumlah_peserta'];
  }
}
