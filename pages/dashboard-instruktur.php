<?php
$img_check = img_icon('check');
$img_next = img_icon('next');
$null_red = '<span class="red consolas miring">null</span>';

$s = "SELECT * FROM tb_penilaian_weekly a 
JOIN tb_penilaian_instruktur b ON a.penilaian=b.penilaian 
WHERE a.id_instruktur = $id_peserta AND a.week=$week 
ORDER BY b.id
";
// echo '<pre>';
// var_dump($s);
// echo '</pre>';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$thead = "
  <thead class='gradasi-toska'>
    <th>No</th>
    <th>Detail Penilaian Instruktur</th>
    <th class=tengah>Basic Poin</th>
    <th>My Multiplier Info</th>
    <th class=kanan>Teaching Points</th>
  </thead>

";
if (mysqli_num_rows($q)) {
  $tr = '';
  $i = 0;
  $total_poin = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $penilaian_show = key2kolom($d['penilaian']);
    $total_poin += $d['my_point'];

    $my_multiplier_info = $d['my_multiplier_info'];
    if (!$my_multiplier_info) $my_multiplier_info = $null_red;
    $my_point = number_format($d['my_point']);
    $my_point = $my_point ? "<span class='green bold'>$my_point</span>" : "<span class='f12 abu miring'>0</span>";
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <div class=darkblue>$penilaian_show</div>
          <div class='darkabu miring f14 mb1 mt1'>$d[deskripsi]</div>
          <div class='abu f12'>$d[my_multiplier_info]</div>
        </td>
        <td class=tengah>$d[poin]</td>
        <td class='darkabu f14'>$my_multiplier_info <a href='?$d[redirect_to]'>$img_next</a></td>
        <td class=kanan>$my_point</td>
      </tr>
    ";
  }

  $total_poin_show = number_format($total_poin);

  # ============================================================
  # FINAL ECHO :: INSTRUKTUR POINTS
  # ============================================================
  echo "
  <div data-aos=fade-up>
    <p>Berikut adalah rekap mingguan Teaching Point Instruktur dari seluruh Room Anda</p>
    <table class='table table-hover table-striped'>
      $thead
      $tr
      <tr class='gradasi-toska f20 tengah'>
        <td colspan=4 class=darkblue>
          <div class=p2>Total Teaching Points</div>
        </td>
        <td colspan=4 class='kanan'>
          <div class='green bold p2'>$total_poin_show</div>
        </td>
      </tr>
    </table>
  </div>
  ";
} else {

  # ============================================================
  # AUTO-SAVE CALCULATION INSTRUKTUR POINTS
  # ============================================================
  include 'dashboard-instruktur-auto_save_penilaian.php';
}
