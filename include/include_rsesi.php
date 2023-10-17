<?php
if(isset($cn)){
  $s = "SELECT id,tags,nama FROM tb_sesi order by no";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $rsesi = [];
  while ($d=mysqli_fetch_assoc($q)){
    $rsesi[$d['id']] = $d['nama'];
    $rtags[$d['id']] = $d['tags'];
  } 
}