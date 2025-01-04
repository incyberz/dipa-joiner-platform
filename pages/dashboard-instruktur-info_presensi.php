<?php
if (!$room_count['count_presensi_aktif'] > 1) {
  $info_presensi = div_alert('danger', 'Belum ada kalkulasi [INFO-presensi] untuk P2');
} else {
  $no_sesi = $room_count['count_presensi_aktif'];

  $tmp = explode(';', $room_count['arr_count_peserta_kelas']);
  $progres = '';
  foreach ($tmp as $k => $v) {
    if ($v) {
      $tmp2 = explode('=', $v);
      $kelas = $tmp2[0];
      $jumlah_presenter = $tmp2[1];
      $jumlah_peserta_kelas = $tmp2[2];

      $jumlah_presenter = $jumlah_peserta_kelas;
      if ($jumlah_presenter == $jumlah_peserta_kelas) {
        $persen = 100;
        $info_persen = "$jumlah_presenter $Peserta (100%) <i style='display:inline-block;margin: 0 0 5px 25px'>$img_check</i>";
      } else {
        $persen = $jumlah_peserta_kelas ? round($jumlah_presenter * 100 / $jumlah_peserta_kelas) : 0;
        $info_persen = "<span class=f20>$jumlah_presenter</span> of $jumlah_peserta_kelas ($persen%)";
      }

      $progres .= "
        <div class='mt2 mb1 f12 abu'>
          <a href='?set_target_kelas_dan_presensi&kelas=$kelas'>
            $kelas : $info_persen
          </a>
        </div>
        <div class=progress>
          <div class=progress-bar style='width:$persen%'></div>
        </div>
      ";
    }
  }
  $info_presensi = "<div class='mb2'>Presensi P$no_sesi ($room_count[count_peserta] $Peserta) <a href='?update_room_count'>" . img_icon('refresh') . "</a></div>$progres";
}
echo div_alert('info tengah', $info_presensi);
