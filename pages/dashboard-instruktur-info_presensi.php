<?php
if ($room_count['count_presensi_aktif'] > 1) {
  $info_presensi = div_alert('danger', 'Belum ada kalkulasi [INFO-presensi] untuk P2');
} else {
  $no_sesi = 1;

  echo '<pre>';
  var_dump($room_count);
  echo '</pre>';

  $tmp = explode(';', $room_count['arr_count_peserta_kelas']);
  $progres = '';
  foreach ($tmp as $k => $v) {
    if ($v) {
      $tmp2 = explode('=', $v);
      $kelas = $tmp2[0];
      $jumlah_peserta_kelas = $tmp2[1];
      $progres .= "
        <div class='mt2 mb1 f12 abu'>$kelas : 34 of $jumlah_peserta_kelas (67%)</div>
        <div class=progress>
          <div class=progress-bar style='width:67%'></div>
        </div>
      ";
    }
  }
  $info_presensi = "<div class='mb2'>Presensi P$no_sesi</div>$progres";
}
echo div_alert('info tengah', $info_presensi);
