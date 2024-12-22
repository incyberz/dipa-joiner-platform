<?php
if ($sesi['jadwal_kelas']) {
  $status_pelaksanaan = eta2($sesi['jadwal_kelas']);
  $jadwal_kelas_show = hari_tanggal($sesi['jadwal_kelas']);
} else {
  $jadwal_kelas_show = $null;
  if ($id_role == 1) {
    $link_encoded = urlencode(get_current_url());
    $text_wa = "Yth. $Bapak $trainer[nama], saya $user[nama] ingin melaporkan bahwa Jadwal Kuliah di LMS untuk sesi $sesi[no] belum ditentukan. Terimakasih.%0a%0aLink:%0a$link_encoded%0a%0aFrom: DIPA Joiner System, $datetime";
    $href_wa = href_wa($trainer['no_wa'], $text_wa);
    $set_presensi = "<a class='btn btn-success w-100 mt4' href='$link_wa' onclick='return confirm(`Laporkan?`)'>$img_wa Laporkan</a>";
  } else {
    $set_presensi = "<a href='?presensi' >Set</a>";
  }
  $status_pelaksanaan = div_alert('danger', "Jadwal Kelas untuk sesi ini belum ditentukan. $set_presensi");
  $sesi['jadwal_kelas'] ? '' : '<span class="f12 miring abu">belum dilaksanakan</span>';
}

$closing = eta2($sesi['akhir_presensi']);

$ui_jadwal = "
  <div  class='mt1 pt1 mb4' style='border-top:solid 3px #cdc'>
    <div class='f10 abu'>
      <div>
        <b class=darkblue>Jadwal Kelas</b> 
        [ $kelas ]
      </div>
      $jadwal_kelas_show ($status_pelaksanaan)
    </div>
    <div class='kecil miring abu f10'>
      Closing Presensi: $closing
    </div>
  </div>
";
