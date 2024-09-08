<?php
if ($room_count['count_presensi_aktif'] > 1) {
  $info_pekan = div_alert('danger', 'Belum ada kalkulasi [INFO-PEKAN] untuk P2');
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
    $info_pekan = "<div><a onclick='return confirm(`Menuju Learning Path?`)' href='?list_sesi'>$nama_sesi</a></div><div class='f12 abu mb1'>$mode di Kelas $kelas_pertama pada $hari | $eta</div>";
  } else {
    $info_pekan = '<span class=red>no data sesi pertemuan 1 atau sesi kelas belum ada</span><hr>';
  }
}
echo div_alert('info tengah', "
  <div class='f12 abu'>Pekan ke-$week</div>
  $info_pekan
");
