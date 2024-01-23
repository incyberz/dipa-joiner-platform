<?php
# =================================================================
instruktur_only();
$mode = $_GET['mode'] ?? '';
$rmode = ['PG','TF','MC','ESSAY'];
$li_mode = '';
$a_mode = '';
foreach ($rmode as $m) {
  $li_mode .= "<li><a href='?tambah_ujian&mode=$m'>$m</a></li>";
  $a_mode .= "<a href='?tambah_ujian&mode=$m'>$m</a> | ";
}
if($mode==''){
  echo "<ul>$li_mode</ul>";
}else{
  $mode_lowercase = strtolower($mode);
  echo $a_mode;
  include "tambah_ujian_$mode_lowercase.php";
}

















?>
<script>
  $(function(){

  })
</script>
