<?php
if(isset($cn)){
  $s = "SELECT id,tags,nama,no FROM tb_sesi order by no";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $arr_sesi = [];
  while ($d=mysqli_fetch_assoc($q)){
    $arr_sesi[$d['id']] = $d['nama'];
    $arr_tags[$d['id']] = $d['tags'];
    $arr_no_sesi[$d['id']] = $d['no'];
  } 
}