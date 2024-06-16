<?php
$info_room = '';
if ($jumlah_sesi) {
  $awal_sesi = $d_room['awal_sesi'];

  if ($awal_sesi) {
    $tanggal = date('d-F-Y H:i', strtotime($awal_sesi));
    $info_room .= div_alert('success', "Awal sesi room pada tanggal $tanggal.");
  } else {
    $info_room .= div_alert('danger', "Awal sesi pada room belum ada (sepertinya ini room lama).");
  }
  $info_room .= div_alert('success', "Sudah ada $jumlah_sesi sesi aktif pada room ini.");

  // cari tanggal pada sesi pertama
  $s = "SELECT awal_presensi FROM tb_sesi WHERE id_room=$id_room AND no=1 -- pertemuan 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  if ($d) {
    $awal_presensi = $nama_hari[date('w', strtotime($d['awal_presensi']))] . ', ' . date('d-M-Y H:i', strtotime($d['awal_presensi']));
    $info_room .= div_alert('success', "Awal presensi di set ke hari $awal_presensi");
  } else {
    $info_room .= div_alert('danger', "Awal presensi di sesi 1 masih kosong.");
  }

  $w = date('w', strtotime($d['awal_presensi']));
  if ($w == 0) $add = 1;
  if ($w > 1) $add = 1 - $w;

  $senin_pertama = date('Y-m-d H:i', strtotime("$add day", strtotime($d['awal_presensi'])));

  $inputs = "
    $info_room 
    <input type=hidden name=awal_sesi value='$senin_pertama'>
  ";
} else {
  if (!$d_room['minggu_normal_uts']) {
    die(div_alert('danger', "Data Room minggu_normal_uts belum lengkap."));
  } else {
    $inputs = '';
    $inputs .= "
      <input class='bg-yellow' type='hidden' name=awal_sesi id=awal_sesi required />
      <div class='mb1'>Senin pada Minggu Pertama tanggal:</div>
      <div class='flexy'>
        <div>
          <input class='form-control awal_sesi_trigger' type='date' id=tgl_awal_sesi />
        </div>
        <div>
          <input class='form-control awal_sesi_trigger' type='time' id=jam_awal_sesi min='7:00' max='22:00' value='08:00' />
        </div>
        <div>
          <input required type=number min=30 max=240 step=5 name=durasi_tatap_muka class='form-control mb1' placeholder='Durasi tatap muka...' value=90 style='width:80px' />
        </div>
        <div class='abu miring pt1'>
          menit
        </div>
      </div>
      
      <div class='f14 abu miring mb1'>)* usahakan awal minggu adalah hari Senin</div>
      <div class='f14 abu miring mb1'>)* default awal pembelajaran adalah pukul 08:00</div>
      <div class='f14 abu miring mb3'>)* default durasi tatap muka adalah 90 menit (2 SKS)</div>
  
      <div class='mt2 mb4'>
        <label>
          <input type=checkbox required />
          Saya menyatakan bahwa tanggal dan jam diatas sudah benar (sesuai dengan Jadwal Pembelajaran sebenarnya).
        </label>
      </div>
      <script>
        $(function() {
          $('.awal_sesi_trigger').change(function() {
            $('#awal_sesi').val(
              $('#tgl_awal_sesi').val() +
              ' ' +
              $('#jam_awal_sesi').val()
            );
          })
        })
      </script>
    ";
  }
}
