<?php
if (!$room_count['count_bukti_latihan']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada satupun Peserta yang mengumpulkan bukti latihan</div>
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
    <div>$room_count[count_bukti_latihan_verified] dari $room_count[count_bukti_latihan] bukti latihan terperiksa</div>
    $ui_check
  </a>
");
