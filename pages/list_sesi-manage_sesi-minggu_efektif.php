<?php
# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_reset_minggu_efektif'])) {
  # ============================================================
  # RESET STATUS ROOM
  # ============================================================  
  $s = "UPDATE tb_room SET 
  status=NULL,
  awal_sesi = NULL  
  WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  # ============================================================
  # RESET AWAL PRESENSI
  # ============================================================
  $s = "UPDATE tb_sesi SET 
  awal_presensi=NULL, 
  akhir_presensi=NULL 
  WHERE id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  # ============================================================
  # RESET JADWAL KELAS
  # ============================================================
  jsurl();
}

$s = "SELECT 
a.*,
b.nama as jenis_sesi
FROM tb_sesi a 
JOIN tb_jenis_sesi b ON a.jenis=b.jenis 
WHERE a.id_room=$id_room 
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$divs = '';
$nav = '';
$no_sesi_normal = 0;
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $durasi = durasi_hari($d['awal_presensi'], $d['akhir_presensi']);
  $eta = eta2($d['awal_presensi']);

  $divs .= "
    <tr>
      <td>$i</td>
      <td>$d[jenis_sesi]</td>
      <td>$d[nama]</td>
      <td>
        $d[awal_presensi]
        <div class='f12 abu miring'>$eta</div>
      </td>
      <td>$d[akhir_presensi]</td>
      <td>$durasi hari</td>
    </tr>
  ";
}

$info = $arr_part[$part]['desc'];
$perhatian = div_alert('danger darkred bold mt4', "Perhatian! Jika Anda ingin mengubah Seting minggu efektif maka status room akan berubah menjadi status 2 (Belum Aktivasi Minggu Efektif) dan Anda harus mengulangi Aktivasi Room dari status tersebut. ");

echo "
  <div>
    $nav
  </div>
  <div class='alert alert-info biru bold tengah'><span class=biru>$info</div>
  <div class='gradasi-hijau br5 p1 pt0 br10'>
    <table class='table td_trans td_toska '>
      <thead>
        <th>Minggu</th>
        <th>Jenis</th>
        <th>Nama</th>
        <th>Awal Presensi</th>
        <th>Akhir Presensi</th>
        <th>Durasi</th>
      </thead>
      $divs
    </table>
  </div>

  $perhatian

  <form method=post>
    <button class='btn btn-danger w-100' name=btn_reset_minggu_efektif onclick='return confirm(`Reset minggu efektif?`)'>Reset Minggu Efektif dan Re-Activate Room</button>
  </form>

";
