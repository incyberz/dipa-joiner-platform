<?php
# =================================================================
instruktur_only();
$mode = $_GET['mode'] ?? '';
$rmode = ['pg', 'tf', 'mc', 'essay'];
$li_mode = '';
$a_mode = '';
foreach ($rmode as $m) {
  $li_mode .= "<a class='btn btn-success w-100 mb2 upper' href='?tambah_ujian&mode=$m'>$m</a>";
  $a_mode .= "<a href='?tambah_ujian&mode=$m'>$m</a> | ";
}
if ($mode == '') {
  echo "<ul>$li_mode</ul>";
} else {
  $mode_lowercase = strtolower($mode);
  echo $a_mode;
  include "tambah_soal_$mode_lowercase.php";
}

















?>
<script>
  $(function() {

  })
</script>