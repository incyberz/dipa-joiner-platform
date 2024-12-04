<?php
if (!$sesi_aktif) {
  echo div_alert('danger', "Tidak ada sesi aktif untuk hari ini.<hr>Silahkan <a href='?presensi'>Manage Penanggalan Presensi</a>");
  exit;
}


if ($room_count['count_presensi_aktif'] > 1) {
  $info_pekan = div_alert('danger', 'Belum ada kalkulasi [INFO-PEKAN] untuk P2');


  if ($sesi_aktif['jenis'] === '0') { // sesi tenang
    if ($room_count['count_presensi'] == $room_count['count_presensi_aktif']) { // UAS
      $info_pekan = "Sesi Tenang Pra-UAS";
    } else {
      $info_pekan = "Sesi Tenang Pra-UTS";
    }
  } elseif ($sesi_aktif['jenis'] === '2') { // pekan UTS
    $info_pekan = "Sesi UTS";
  } elseif ($sesi_aktif['jenis'] === '3') { // pekan UTS
    $info_pekan = "Sesi UAS";
  } elseif ($sesi_aktif['jenis'] === '1') { // pekan UTS 

    $s = "SELECT * 
    FROM tb_room_kelas a  
    -- JOIN tb_sesi_kelas b ON a.kelas=b.kelas -- terdapat Room lama yang belum set jadwal kuliah
    WHERE a.id_room=$id_room 
    AND a.kelas!= 'INSTRUKTUR' 
    AND a.ta=$ta 
    -- AND b.id_sesi = $sesi_aktif[id]
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $info_kelas = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $s2 = "SELECT jadwal_kelas, is_terlaksana FROM tb_sesi_kelas WHERE kelas='$d[kelas]' AND id_sesi=$sesi_aktif[id]";
      // echo "<hr>$s2 ZZZ HERE";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $d2 = mysqli_fetch_assoc($q2);

      $eta = eta2($d2['jadwal_kelas']);
      $hari = hari_tanggal($d2['jadwal_kelas'], 1, 0);
      $info_kelas .= "
        <div>
          $d[kelas] - $hari - $eta 
          <a target=_blank href='?set_jadwal_kelas&kelas=$d[kelas]'>$img_edit</a>
        </div>
      ";
    }



    $info_pekan = "
      <div><a href='?list_sesi'>Pertemuan ke-$sesi_aktif[sesi_normal_count] $sesi_aktif[nama]</a></div>
      <div class='f12 mt2'>$info_kelas</div>
    ";
  } else {
    echo '<pre>';
    var_dump($sesi_aktif);
    echo '</pre>';
    die('<hr>Invalid jenis sesi.');
  }
} else {
  $nama_sesi = "Siap-siap untuk Pertemuan Pertama!";
  $mode = 'Tatap Muka';

  # ============================================================
  # PERTEMUAN PERTAMA
  # ============================================================
  $s = "SELECT 
  b.jadwal_kelas as jadwal_kelas_pertama, 
  b.kelas as kelas_pertama 
  FROM tb_sesi a 
  JOIN tb_sesi_kelas b ON a.id=b.id_sesi 
  WHERE a.no=1 AND a.id_room=$id_room
  ORDER BY b.jadwal_kelas LIMIT 1
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $d = mysqli_fetch_assoc($q);
    $jadwal_kelas_pertama = $d['jadwal_kelas_pertama'];
    $kelas_pertama = $d['kelas_pertama'];
    $hari = hari_tanggal($jadwal_kelas_pertama, 1, 1, 0);
    $eta = eta2($jadwal_kelas_pertama);
    $info_pekan = "
      <div>$nama_sesi</div>
      <div class='f12 abu mb1'>
        $mode di Kelas $kelas_pertama pada $hari | $eta
      </div>
    ";
  } else {
    $info_pekan = '<span class=red>no data sesi pertemuan 1 atau sesi kelas belum ada</span><hr>';
  }
}
echo div_alert('info tengah', "
  <div class='f12 abu'>Pekan ke-$week</div>
  $info_pekan
");
