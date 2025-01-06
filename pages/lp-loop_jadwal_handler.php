<?php
if ($id_role == 1) {
  if ($sesi['jadwal_kelas']) {
    $status_pelaksanaan = eta2($sesi['jadwal_kelas']);
    $jadwal_kelas_show = hari_tanggal($sesi['jadwal_kelas']);
  } else {
    $jadwal_kelas_show = "<i class='f12 red'>belum ada jadwal kelas</i>";
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
} elseif ($id_role == 2) {

  $s2 = "SELECT * FROM tb_room_kelas a 
  JOIN tb_sesi_kelas b ON a.kelas=b.kelas
  WHERE a.ta=$ta AND a.id_room=$id_room AND a.kelas !='INSTRUKTUR' 
  AND id_sesi = $id_sesi";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $li = '';
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $eta = eta2($d2['jadwal_kelas']);
    $hari = hari_tanggal($d2['jadwal_kelas']);
    $li .= "
      <li>
        <b>$d2[kelas]: </b> $hari $eta
      </li>
    ";
  }
  $jadwal_kelas_show = "<ul class='p0 m0 pl2'>$li</ul>";
  $status_pelaksanaan = '';
}

$closing = eta2($sesi['akhir_presensi']);

if ($id_role == 1) {
  if ($sesi['my_presensi'] === '0') {
    $presensi_saya = "Presensi Saya: <i>Hadir Telat $img_warning</i>";
  } elseif ($sesi['my_presensi']) {
    $presensi_saya = "Presensi Saya: <i class=green>Hadir Ontime $img_check</i>";
  } else {
    $presensi_saya = "Presensi Saya: <i class=red>Belum Presensi $img_warning</i>";
  }
} elseif ($id_role == 2) {
  if ($sesi['id'] == ($sesi_aktif['id'] ?? null)) {
    require_once 'progress_bar_presensi.php';
    $presensi_saya = progress_bar_presensi($room_count['arr_count_peserta_kelas']);
  } else {
    $presensi_saya = "<a href='?presensi_rekap'>Go to Rekap Presensi</a>";
  }
}

$ui_jadwal = "
  <div  class='mt1 pt1 mb4 f10 abu' style='border-top:solid 3px #cdc'>
    <div class=''>
      <div>
        <b class=darkblue>Jadwal Kelas</b> 
        [ $kelas ]
      </div>
      $jadwal_kelas_show $status_pelaksanaan
    </div>
    <div class='miring '>
      <a href='?presensi'>$presensi_saya</a>
    </div>
    <div class='miring '>
      Deadline Presensi: $closing
    </div>
  </div>
";
