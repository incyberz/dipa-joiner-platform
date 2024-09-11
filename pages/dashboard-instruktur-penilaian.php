<?php
# ============================================================
# DASHBOARD - INSTRUKTUR - PENILAIAN
# ============================================================
$s = "SELECT a.*,
(
  SELECT count(1) FROM tb_penilaian_weekly 
  WHERE 1 -- id_penilaian=a.id 
  AND id_instruktur=$id_peserta
  AND id_room=$id_room
  AND week=$week
  ) count_penilaian
FROM tb_penilaian_instruktur a 
ORDER BY a.id
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$thead = "
  <thead class='gradasi-toska'>
    <th>No</th>
    <th>Detail Penilaian Instruktur</th>
    <th class='tengah desktop_only'>Basic Poin</th>
    <th class='desktop_only'>My Multiplier Info</th>
    <th class=kanan>Teaching Points</th>
  </thead>
";
$tr = '';
$i = 0;
$total_poin = 0;
while ($d = mysqli_fetch_assoc($q)) {

  if (!$d['count_penilaian']) {
    # ============================================================
    # AUTO-SAVE CALCULATION INSTRUKTUR POINTS
    # ============================================================
    include 'dashboard-instruktur-auto_save_penilaian.php';
    exit;
  }

  $i++;
  $penilaian_show = key2kolom($d['penilaian']);

  $s2 = "SELECT * FROM tb_penilaian_weekly WHERE kode = '$d[id]-$id_peserta-$id_room-$week'";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $d2 = mysqli_fetch_assoc($q2);


  $total_poin += $d2['my_point'] ?? 0;

  $my_multiplier_info = $d2['my_multiplier_info'] ?? '';
  if (!$my_multiplier_info) $my_multiplier_info = $null_red;
  $my_point = number_format($d2['my_point'] ?? 0);
  $my_point = $my_point ? "<span class='green bold'>$my_point</span>" : "<span class='f12 abu miring'>0</span>";
  $tr .= "
    <tr>
      <td>$i</td>
      <td>
        <div class=darkblue>$penilaian_show</div>
        <div class='darkabu miring f14 mb1 mt1'>$d[deskripsi]</div>
        <div class='abu f12 desktop_only'>$d[multiplier_info]</div>
      </td>
      <td class='tengah desktop_only'>$d[poin]</td>
      <td class='darkabu f14 desktop_only'>$my_multiplier_info <a href='?$d[redirect_to]'>$img_next</a></td>
      <td class=kanan>$my_point</td>
    </tr>
  ";
}

$total_poin_show = number_format($total_poin);

# ============================================================
# FINAL ECHO :: INSTRUKTUR POINTS
# ============================================================
$img_refresh = img_icon('refresh');
echo "
<div data-aos=fade-up>
  <hr>  
  <h3 class='darkblue tengah'>Teaching Points</h3>
  <div class='tengah mb2'>Pada Room <span class=darkblue>$nama_room</span></div>
  <div class='green bold p2 gradasi-toska f20 tengah mb2'>$total_poin_show TP</div>
  <div class='flexy flex-center mb4'>
    <div class='btn_aksi pointer text-hover-bold' id=detail_TP__toggle>$img_detail details</div> 
    <div ><a onclick='return confirm(`Reupdate Teaching Points minggu ini?`)' href='?dashboard-instruktur-auto_save_penilaian' class=text-hover-bold>$img_refresh reupdate</a></div>
  </div> 
  <div class='hideit' id=detail_TP>

    <p class=tengah>Detail Rekap mingguan Teaching Point dari seluruh Room Anda</p>
    <table class='table table-hover table-striped'>
      $thead
      $tr
      <tr class=' f20 tengah'>
        <td colspan=100% class=darkblue>
          <div class=p2>Total Teaching Points</div>
        </td>
      </tr>
      <tr class='gradasi-toska f20 tengah'>
        <td colspan=100% >
          <div class='green bold p2'>$total_poin_show TP</div>
        </td>
      </tr>
    </table>
  </div>
</div>
";
