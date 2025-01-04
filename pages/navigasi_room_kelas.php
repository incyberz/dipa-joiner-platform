<?php
if (!$param_awal) die('Dibutuhkan param_awal untuk navigasi-room-kelas.');
if (!isset($get_kelas)) die('Dibutuhkan get_kelas untuk navigasi-room-kelas.');
$s = "SELECT b.* 
FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas 
WHERE a.id_room=$id_room 
AND b.kelas != 'INSTRUKTUR' 
AND b.status = 1 -- kelas aktif 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  die("$Room ini belum punya $Room-kelas.");
} else {
  $li = '';
  $btn_class = $get_kelas ? '' : 'btn btn-success';
  while ($d = mysqli_fetch_assoc($q)) {
    $reg = strtoupper($d['shift']) == 'P' ? 'REG' : 'NR';
    $font_size = $d['kelas'] == $get_kelas ? 'f16 blue bold' : 'f10';
    $kelas_show = "$d[prodi]-$reg-SM$d[semester]";
    $kelas_show = $d['caption'];
    $kelas_show = str_replace("~$ta", '', $kelas_show);
    $li .= "
      <li>
        <a class='$font_size $btn_class' href='?$param_awal&kelas=$d[kelas]'>
          $kelas_show
        </a>
      </li>
    ";
  }
  $pilih_kelas = $get_kelas ? '' : '<div class="tengah mb2 bold blue">Silahkan pilih kelas:</div>';
  echo "
  <style>#list_kelas li{list-style:none}</style>
  $pilih_kelas
  <ul class='flexy m0 p0 flex-center mb2' id=list_kelas>$li</ul>
  ";
}
