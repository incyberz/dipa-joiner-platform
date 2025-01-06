<?php
include 'progress_bar_presensi.php';

$info_presensi = "<a href='?presensi'>Presensi P1 (Next Pertemuan)</a><div class='abu f12 tengah'>--no data--</div>";
if ($room_count['count_presensi_aktif'] > 1) {
  $no_sesi = $room_count['count_presensi_aktif'];
  $progres = progress_bar_presensi($room_count['arr_count_peserta_kelas']);
  $info_presensi = "<div class='mb2'>Presensi P$no_sesi ($room_count[count_peserta] $Peserta) <a href='?update_room_count'>" . img_icon('refresh') . "</a></div>$progres";
}
echo div_alert('info tengah', $info_presensi);
