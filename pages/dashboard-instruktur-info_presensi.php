<?php
if (!$room_count['count_presensi_aktif'] > 1) {
  $info_presensi = div_alert('danger', 'Belum ada kalkulasi [INFO-presensi] untuk P2');
} else {
  $no_sesi = $room_count['count_presensi_aktif'];

  echo '<pre>';
  var_dump($room_count);
  echo '</pre>';

  $tmp = explode(';', $room_count['arr_count_peserta_kelas']);
  $progres = '';
  foreach ($tmp as $k => $v) {
    if ($v) {
      $tmp2 = explode('=', $v);
      $kelas = $tmp2[0];
      $jumlah_presenter = $tmp2[1];
      $jumlah_peserta_kelas = $tmp2[2];
      $persen = $jumlah_peserta_kelas ? round($jumlah_presenter * 100 / $jumlah_peserta_kelas) : 0;
      $progres .= "
        <div class='mt2 mb1 f12 abu'>
          <a href='?set_target_kelas_dan_presensi&kelas=$kelas'>
            $kelas : $jumlah_presenter of $jumlah_peserta_kelas ($persen%)
          </a>
        </div>
        <div class=progress>
          <div class=progress-bar style='width:$persen%'></div>
        </div>
      ";
    }
  }
  $info_presensi = "<div class='mb2'>Presensi P$no_sesi</div>$progres";
}
echo div_alert('info tengah', $info_presensi);
