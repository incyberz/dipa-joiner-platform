<?php
function progress_bar_presensi($arr_count_peserta_kelas, $img_check = null, $img_warning = null, $Peserta = 'Peserta')
{
  $tmp = explode(';', $arr_count_peserta_kelas);
  $progres = '';
  foreach ($tmp as $k => $v) {
    if ($v) {
      $tmp2 = explode('=', $v);
      $kelas = $tmp2[0];
      $jumlah_presenter = $tmp2[1];
      $jumlah_peserta_kelas = $tmp2[2];

      if (!$jumlah_presenter) {
        $persen = 0;
        $info_persen = "<span class=red>belum ada yang presensi</span> <i style='display:inline-block;margin: 0 0 5px 25px'>$img_warning</i>";
      } elseif ($jumlah_presenter == $jumlah_peserta_kelas) {
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
  return $progres;
}
