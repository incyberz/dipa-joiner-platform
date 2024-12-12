<?php
if(isset($cn)){
  $s = "SELECT a.kelas,(SELECT count(1) from tb_peserta WHERE kelas=a.kelas) jumlah_peserta FROM tb_kelas a";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $arr_kelas = [];
  while ($d=mysqli_fetch_assoc($q)){
    $arr_kelas[$d['kelas']] = $d['jumlah_peserta'];
  } 
}