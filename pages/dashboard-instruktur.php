<?php
$img_check = img_icon('check');

$s = "SELECT * FROM tb_penilaian_weekly a 
JOIN tb_penilaian_instruktur b ON a.penilaian=b.penilaian 
WHERE a.id_instruktur = $id_peserta AND a.week=$week";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$thead = "
  <thead>
    <th>No</th>
    <th>Penilaian bagi Instruktur minggu ini</th>
    <th class=tengah>Basic Poin</th>
    <th>My Multiplier Info</th>
    <th>My Points</th>
  </thead>

";
if (mysqli_num_rows($q)) {
  $tr = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $penilaian_show = key2kolom($d['penilaian']);

    $multiplier_info = 'MULTIPLIER_INFO';
    $my_point = 'MY_POINT';
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <div class=darkblue>$penilaian_show</div>
          <div class='darkabu miring f14 mb1 mt1'>$d[deskripsi]</div>
          <div class='abu f12'>$d[multiplier_info]</div>
        </td>
        <td class=tengah>$d[poin]</td>
        <td class='darkabu f14'>$multiplier_info</td>
        <td>$my_point</td>
      </tr>
    ";
  }

  # ============================================================
  # FINAL ECHO :: INSTRUKTUR POINTS
  # ============================================================
  echo "
  <div data-aos=fade-up>
    <table class='table table-hover table-striped'>
      $thead
      $tr
    </table>
  </div>
  ";
} else {

  # ============================================================
  # AUTO-SAVE CALCULATION INSTRUKTUR POINTS
  # ============================================================
  include 'dashboard-instruktur-auto_save_penilaian.php';
}
