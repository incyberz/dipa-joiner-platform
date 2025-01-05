<?php
if (!$room_count['count_bukti_latihan']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada bukti latihan baru</div>
    <div>$img_warning</div>
  ";
  $notif_type = 'warning';
} elseif ($room_count['count_bukti_latihan_verified'] == $room_count['count_bukti_latihan']) {
  $ui_check = "
    <div class='f12 abu mb1'>Semua Bukti Latihan sudah Anda verifikasi.</div>
    <div>$img_check</div>
  ";
  $notif_type = 'success';
} else {
  $count = $room_count['count_bukti_latihan'] - $room_count['count_bukti_latihan_verified'];
  $ui_check = "
    <div class=mb1>Ada <span class='darkred f20'>$count</span> Latihan yang harus Anda periksa</div>
    <div>$img_next</div>
  ";
  $notif_type = 'danger';
}
echo div_alert("$notif_type tengah", "
  <a href='?verif'>
    $ui_check
    <div class='mt4 f10'>$room_count[count_bukti_latihan_verified] bukti latihan terperiksa</div>
  </a>
");
