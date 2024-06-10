<?php

if (!$d_room['jumlah_sesi_uts']) {
  die(div_alert('danger', "Data Room jumlah_sesi_uts belum lengkap."));
} else {
  $inputs = '';
  $inputs .= "
    <input class='bg-yellow' type='hidden' name=awal_sesi id=awal_sesi required />
    <div class='mb1'>Pertemuan Pertama tanggal:</div>
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
    
    <div class='f12 abu miring mb3'>)* default durasi tatap muka adalah 90 menit (2 SKS)</div>

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
