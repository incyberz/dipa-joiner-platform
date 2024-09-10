<?php
if (!$room_count['count_peserta']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada satupun Peserta yang upload War Profil</div>
    <div>$img_warning</div>
  ";
  $notif_type = 'warning';
} elseif ($room_count['count_peserta_war_image_ok'] == $room_count['count_peserta']) {
  $ui_check = "
    <div class='f12 abu mb1'>Semua War Profile sudah Anda verifikasi.</div>
    <div>$img_check</div>
  ";
  $notif_type = 'success';
} else {
  $count = $room_count['count_peserta'] - $room_count['count_peserta_war_image_ok'];
  $ui_check = "
    <div class=mb1>Ada <span class='darkred f20'>$count</span> Profile yang harus Anda periksa</div>
    <div>$img_next</div>
  ";
  $notif_type = 'danger';
}
echo div_alert("$notif_type tengah", "
  <a href='?verifikasi_profil_peserta'>
    <div>$room_count[count_peserta_war_image_ok] War Profil OK dari $room_count[count_peserta] peserta</div>
    $ui_check
  </a>
");
