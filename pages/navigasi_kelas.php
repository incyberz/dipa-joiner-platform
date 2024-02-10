<?php
if(!$param_awal) die('Dibutuhkan param_awal untuk navigasi-room-kelas.');
if(!isset($get_kelas)) die('Dibutuhkan get_kelas untuk navigasi-room-kelas.');
$s = "SELECT b.* FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.id_room=$id_room 
-- AND b.prodi != 'DEV'
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  die('Room ini belum punya kelas.');
}else{
  $font_size = $get_kelas ? 'f10' : 'f16';
  $li = "<li><a class='$font_size' href='?$param_awal' >All Kelas</a></li>";
  while($d=mysqli_fetch_assoc($q)){
    $reg = strtoupper($d['shift'])=='P' ? 'REG' : 'NR';
    $font_size = $d['kelas']==$get_kelas ? 'f16' : 'f10';
    $li.= "<li><a class='$font_size' href='?$param_awal&kelas=$d[kelas]'>$d[prodi]-$reg-SM$d[semester]</a></li>";
  }
  echo "
  <style>#list_kelas li{list-style:none}</style>
  <ul class='flexy m0 p0' id=list_kelas>$li</ul>
  ";
}
