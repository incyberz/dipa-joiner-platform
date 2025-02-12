<?php
$info_room = '';
if ($jumlah_sesi) {
  $awal_sesi = $room['awal_sesi'];

  if ($awal_sesi) {
    $tanggal = date('d-F-Y H:i', strtotime($awal_sesi));
    $info_room .= div_alert('success', "Awal sesi $Room pada tanggal $tanggal.");
  } else {
    $info_room .= div_alert('danger', "Awal sesi pada $Room belum ada (sepertinya ini $Room lama).");
  }
  $info_room .= div_alert('success', "Sudah ada $jumlah_sesi sesi aktif pada $Room ini.");

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

  // $w = date('w', strtotime($d['awal_presensi']));
  // $add = 0;
  // if ($w == 0) $add = 1;
  // if ($w > 1) $add = 1 - $w;

  // $senin_awal_presensi = date('Y-m-d H:i', strtotime("$add day", strtotime($d['awal_presensi'])));

  $inputs = "
    $info_room 
  ";
}
if (1) {
  if (!$room['minggu_normal_uts']) {
    die(div_alert('danger', "Data $Room minggu_normal_uts (jumlah sesi normal sebelum UTS) belum lengkap (masih null)."));
  } else {
    $inputs = '';
    $ta_show = tahun_ajar_show($ta);
    $hari = hari_tanggal($senin_pertama_kuliah, 1, 1, 0);

    $inputs .= "
      <input class='bg-yellow' type='hidden' name=awal_sesi id=awal_sesi required  value='$senin_pertama_kuliah 08:00:00' />
      <div class='mb1'>Awal TA $ta_show adalah $hari.</div>
      <hr>
      <div class='flexy mb2'>
        <div>Awal Pekan untuk $Room ini : </div>
        <div>
          <input class='form-control awal_sesi_trigger' type='date' id=tgl_awal_sesi value='$senin_pertama_kuliah' />
        </div>
      </div>
      
      <div class='flexy mb2'>
        <div>Jam mulai masuk pembelajaran : </div>
        <div>
          <input class='form-control awal_sesi_trigger' type='time' id=jam_awal_sesi min='7:00' max='22:00' value='08:00' />
        </div>
      </div>
      
      <div class='flexy mb2'>
        <div>Durasi tatap muka : </div>
        <div>
          <input required type=number min=30 max=240 step=5 name=durasi_tatap_muka class='form-control mb1' placeholder='Durasi tatap muka...' value=90 style='width:80px' />
        </div>
        <div>
          menit
        </div>
      </div>
      

      <hr>
      
      <div class='f14 abu miring mb1'>)* usahakan awal minggu adalah hari Senin</div>
      <div class='f14 abu miring mb1'>)* default awal pembelajaran adalah pukul 08:00</div>
      <div class='f14 abu miring mb3'>)* default durasi tatap muka adalah 90 menit (2 SKS)</div>
  
      <div class='mt2 mb4'>
        <label>
          <input type=checkbox required />
          Tanggal diatas adalah Awal Pekan, bukan jadwal pelajaran saya.
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
