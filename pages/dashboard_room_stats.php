<?php
$tb_room_count = '';
# ==========================================================
# ROOM STATISTICS
# ==========================================================
$s = "DESCRIBE tb_room_count";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$Field = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($Field, $d['Field']);
}

$s = "SELECT * FROM tb_room_count WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if ($d) {
  $tr_war = '';
  foreach ($Field as $kolom) {
    if (
      $kolom == 'id'
      || $kolom == 'id_room'
    ) continue;
    $value = $d[$kolom] ?? $unset;
    $kolom_show = ucwords(str_replace('_', ' ', $kolom));
    $tr_war .= "
      <tr>
        <td>$kolom_show</td>
        <td>$value</td>
      </tr>
    ";
  }

  $img = img_icon('detail');

  $tb_room_count = "
    <table class='table table-striped f12'>
      $tr_war
    </table>
  ";
} else {
  $tb_room_count = div_alert('info', 'Belum ada Room Statistic untuk room ini.');
}

echo "

<div class='row '>
  <div class='col-xl-8' data-zzz-aos=fade-up data-zzz-aos-delay=150>
    <div class='wadah'>
      <h2 class='mb2 f24 darkblue'>Room Statistics</h2>
      $tb_room_count
    </div>
  </div>

  <div class='col-xl-4' data-zzz-aos=fade-up data-zzz-aos-delay=300>
    <div class='wadah' style='max-width:400px'>
      <h2 class='mb2 f20 darkblue'>Instruktur</h2>
      <div class='wadah tengah'>
        $profil_instruktur
        <div>$nama_instruktur</div>
      </div>
    </div>
  </div>      
</div>
";
