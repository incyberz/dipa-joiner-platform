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
  // $count = $room_count['count_peserta'] - $room_count['count_peserta_war_image_ok'];

  $s = "SELECT 
  1
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  WHERE a.status=1 
  AND a.war_image LIKE '%war_unverified%' 
  AND d.id_room='$id_room'
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $count = mysqli_num_rows($q);

  if ($count) {
    $ui_check = "
    <div class=mb1>Ada <span class='darkred f20'>$count</span> War Profile yang harus Anda periksa</div>
    <div>$img_next</div>
  ";
    $notif_type = 'danger';
  } else {
    $ui_check = "<div class=mb1>Belum ada War Profil Peserta yang harus di cek $img_check</div>";
    $notif_type = 'success';
  }
}
echo div_alert("$notif_type tengah", "
  <a href='?verifikasi_profil_peserta'>
    $ui_check
  </a>
");
