<?php
if ($sesi['jadwal_kelas']) {
  $status_pelaksanaan = eta2($sesi['jadwal_kelas']);
  $jadwal_kelas_show = hari_tanggal($sesi['jadwal_kelas']);
} else {
  $jadwal_kelas_show = $null;
  if ($id_role == 1) {
    $ingin = "ingin melaporkan bahwa Jadwal Kuliah di LMS untuk sesi $sesi[no] belum ditentukan.";
    $href_wa = href_wa(
      $trainer['no_wa'],
      $ingin,
      'REQUEST LMS',
      false,
      false,
      $trainer['nama'],
      $trainer['gender'],
      $user['nama']
    );
    $set_presensi = "<a class='btn btn-success w-100 mt4' href='$link_wa' onclick='return confirm(`Laporkan?`)'>$img_wa Laporkan</a>";
  } else {
    $set_presensi = "<a href='?presensi' >Set</a>";
  }
  $status_pelaksanaan = div_alert('danger', "Jadwal Kelas untuk sesi ini belum ditentukan. $set_presensi");
  $sesi['jadwal_kelas'] ? '' : '<span class="f12 miring abu">belum dilaksanakan</span>';
}

$closing = eta2($sesi['akhir_presensi']);

$presensi_saya = '<i>no-data</i>';

$ui_jadwal = "
  <div  class='mt1 pt1 mb4 f10 abu' style='border-top:solid 3px #cdc'>
    <div class=''>
      <div>
        <b class=darkblue>Jadwal Kelas</b> 
        [ $kelas ]
      </div>
      $jadwal_kelas_show ($status_pelaksanaan)
    </div>
    <div class='miring '>
      Presensi Saya: $presensi_saya
    </div>
    <div class='miring '>
      Closing Presensi: $closing
    </div>
  </div>
";
