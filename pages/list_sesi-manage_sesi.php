<?php
# ============================================================
# ROUTING MANAGE SESI
# ============================================================
if ($part) {
  # ============================================================
  # SINGLE MANAGE SESI | PART SESI
  # ============================================================
  // if (!$id_sesi) die(erid('id_sesi'));
  $part_title = $arr_part[$part]['title'];
  set_title("$part_title - Manage Sesi");

  echo div_alert('info tengah', "
    <h4> <div class='tebal mt1'>Manage $part_title</div></h4> 
    <a href='?list_sesi&mode=$mode'>$img_prev</a>
  ");

  // if ($part == 'urutan_sesi') $part = 'deskripsi'; // urutan dan deskripsi disatukan
  $src = "$lokasi_pages/list_sesi-manage_sesi-$part.php";
  if (file_exists($src)) {
    include $src;
    include 'list_sesi-manage_sesi-script.php';
  } else {
    echo div_alert('danger', "Belum ada Form untuk editing $part_title ");
  }
} else {
  # ============================================================
  # MANAGE SESI ROUTING
  # ============================================================
  set_title('Manage Sesi');
  echo div_alert('info tengah', "
    <h4 class=mb2>Manage Sesi</h4>
    <a href=?list_sesi>$img_prev</a>
    <hr>
    <div class='tebal biru'>Mana yang ingin Anda atur ?</div>
  ");

  $col = '';
  foreach ($arr_part as $part => $arr_value) {
    $col .= "
      <div class='col-md-4 col-lg-3 col-xl-2 '>
        <a href='?list_sesi&mode=$mode&part=$part'>
          <img src='$lokasi_img/ilustrasi/$arr_value[image]' class=img_ilustrasi>
          <div  class='btn btn-success w-100 mb1 mt2'>
            $arr_value[title]
          </div>
        </a>
        <div class='f12 abu tengah mb4'>$arr_value[desc]</div>
      </div>
    ";
  }

  echo "<div class='row tengah'>$col</div>";
}
