<?php
$div_wars = '';
$div_kbm = '';
$unset = '<span class="consolas f12 abu miring">-</span>';

# ==========================================================
# DATA WARS
# ==========================================================
$s = "DESCRIBE tb_war_summary";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$Field = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($Field, $d['Field']);
}

$s = "SELECT * FROM tb_war_summary WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if ($d) {
  $tr_war = '';
  $war_points = $d['war_points'];
  foreach ($Field as $kolom) {
    if (
      $kolom == 'id'
      || $kolom == 'id_peserta'
      || $kolom == 'id_room'
    ) continue;
    $value = $d[$kolom];
    $kolom_show = ucwords(str_replace('_', ' ', $kolom));
    $tr_war .= "
      <tr>
        <td>$kolom_show</td>
        <td>$value</td>
      </tr>
    ";
  }

  $img = img_icon('detail');
  $war_points_show = number_format($d['war_points']);

  $div_wars = "
    <h3 class='mb2 mt2 darkblue f18'>War Points : $war_points_show LP <span class=btn_aksi id=war_points__toggle>$img</span></h3>
    <div class='wadah gradasi-hijau hideit' id=war_points>
      <table class='table table-striped f12'>
        <div class=mb2>War Point Details</div>
        $tr_war
      </table>
    </div>
  ";
} else {
  $div_wars = div_alert('info', 'Belum ada Wars pada room ini.');
}

# ==========================================================
# POIN KBM
# ==========================================================
$s = "DESCRIBE tb_poin";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$Field = [];
while ($d = mysqli_fetch_assoc($q)) {
  array_push($Field, $d['Field']);
}

$s = "SELECT * FROM tb_poin WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if ($d) {
  $tr_kbm = '';
  foreach ($Field as $kolom) {
    if (
      $kolom == 'id'
      || $kolom == 'id_peserta'
      || $kolom == 'id_room_kelas'
      || $kolom == 'id_room'
    ) continue;
    $value = $d[$kolom];
    if ($value == '') {
      $value_show = $unset;
    } else {
      $value_show = $value;
    }
    $kolom_show = ucwords(str_replace('_', ' ', $kolom));
    if (
      $kolom_show == 'Uts'
      || $kolom_show == 'Uas'
      || $kolom_show == 'Remed Uts'
      || $kolom_show == 'Remed Uas'
      || $kolom_show == 'Nilai Akhir'
    ) $kolom_show = strtoupper($kolom_show);
    $tr_kbm .= "
      <tr>
        <td>$kolom_show</td>
        <td>$value_show</td>
      </tr>
    ";
  }

  $kbm_points = $my_points - $war_points;
  $kbm_points_show = number_format($kbm_points);

  $div_kbm = "
    <h3 class='mb2 mt2 darkblue f18'>KBM Points : $kbm_points_show LP <span class=btn_aksi id=kbm_points__toggle>$img</span></h3>
    <div class='wadah gradasi-hijau hideit' id=kbm_points>
      <table class='table table-striped f12'>
        <div class=mb2>Detail Nilai KBM</div>
        $tr_kbm
      </table>
    </div>
  ";
} else {
  $div_kbm = div_alert('info', 'Belum ada Nilai KBM pada room ini.');
}









?>
<div class="wadah mb2 p2 tengah">
  <h2 class="f24 darkblue m2">My Points</h2>
  <div class='my_points gradasi-toska p2 mb2 border-top border-bottom'><?= $my_points_show ?> LP</div>
  <p class="small fst-italic">Poin didapatkan dari seluruh aktifitas pembelajaran semisal latihan, challenge, bertanya, presensi, dan Wars.</p>

  <?= $div_wars ?>
  <?= $div_kbm ?>

</div>