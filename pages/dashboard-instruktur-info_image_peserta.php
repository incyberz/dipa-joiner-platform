<?php
if (!$room_count['count_peserta']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada satupun Peserta yang upload Image Profil</div>
    <div>$img_warning</div>
  ";
  $notif_type = 'warning';
} elseif ($room_count['count_peserta_image_ok'] == $room_count['count_peserta']) {
  $ui_check = "
    <div class='f12 abu mb1'>Semua Image Profile sudah Anda verifikasi.</div>
    <div>$img_check</div>
  ";
  $notif_type = 'success';
} else { // ada $Peserta dan profil_ok != count_peserta

  $s = "SELECT 1
  FROM tb_peserta a 
  JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
  JOIN tb_kelas c ON b.kelas=c.kelas 
  JOIN tb_room_kelas d ON c.kelas=d.kelas 
  WHERE a.status=1 
  AND a.profil_ok is null   -- belum diperiksa
  AND a.image is not null   -- _peserta sudah upload
  AND c.ta = $ta  -- tahun ajar saat ini
  AND c.kelas != 'INSTRUKTUR'
  AND d.id_room=$id_room -- di $Room ini
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $count = mysqli_num_rows($q);

  if ($count) {
    $ui_check = "
      <div class=mb1>Ada <span class='darkred f20'>$count</span> Profile yang harus Anda periksa</div>
      <div>$img_next</div>
    ";
    $notif_type = 'danger';
  } else {
    $ui_check = "<div class=mb1>Belum ada Profil Peserta yang harus di cek $img_check</div>";
    $notif_type = 'success';
  }
}
echo div_alert("$notif_type tengah", "
  <a href='?verifikasi_profil_peserta'>
    $ui_check
  </a>
");
