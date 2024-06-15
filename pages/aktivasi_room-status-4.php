<?php

function tanggal_sesi_show($no_minggu, $awal_sesi, $jeda_sesi = 7, $jenis_sesi = 1)
{
  if ($no_minggu < 1) return false;

  $selisih = $jeda_sesi * ($no_minggu - 1);
  $tanggal_sesi = date('Y-m-d H:i',  strtotime("+$selisih day", strtotime($awal_sesi)));
  $input_awal_sesi = "<input type=hidden name=awal_presensi[$no_minggu] value='$tanggal_sesi--$jenis_sesi'>";
  return date('d-M-Y, H:i', strtotime($tanggal_sesi)) . $input_awal_sesi;
}

# ============================================================
# PRE FORM
# ============================================================
$awal_sesi = $d_room['awal_sesi'];
$awal_sesi_show = '<span class="tebal biru">' . $nama_hari[date('w', strtotime($awal_sesi))] . ', ' . date('F-d, Y, H:i', strtotime($awal_sesi)) . '</span> | ' . eta2($d_room['awal_sesi']);
$mingguan = $Minggu . 'an';
$pre_form = "
  <form method=post class='wadah bg-white'>
    <div class=' flexy'>
      <div>
        Awal Sesi : $awal_sesi_show 
      </div>
      <div>
        <button name=reset_awal_sesi class='btn btn-danger btn-sm' onclick='return confirm(`Yakin untuk Reset Awal Sesi?`)'>Reset Awal Sesi</button>
      </div>
    </div>
    <div class=mt2>Sesi dilakukan secara <u class='tebal darkblue'>$mingguan</u>, next sesi adalah +$jeda_sesi hari dari sesi sebelumnya.</div>
  </form>
";

# ============================================================
# AKTIVASI JADWAL SESI
# ============================================================
if (!$d_room['awal_sesi']) {
  die(div_alert('danger', "Data Room awal sesi belum ada."));
} else {
  $inputs = '';
  $no_sesi_harian = 0;
  $no_minggu = 0;
  $tr = '';
  $arr = [
    'uts' => [
      'caption' => 'UTS',
      'jumlah_sesi' => $d_room['minggu_normal_uts'],
      'minggu_tenang' => $d_room['minggu_tenang_uts'],
      'durasi_ujian' => $d_room['durasi_uts'],
    ],
    'uas' => [
      'caption' => 'UAS',
      'jumlah_sesi' => $d_room['minggu_normal_uas'],
      'minggu_tenang' => $d_room['minggu_tenang_uas'],
      'durasi_ujian' => $d_room['durasi_uas'],
    ],
  ];

  foreach ($arr as $musim => $arr_musim) {
    $nama_musim = $arr_musim['caption'];
    $jumlah_sesi = $arr_musim['jumlah_sesi'];
    if ($jumlah_sesi) {
      for ($i = 1; $i <= $jumlah_sesi; $i++) {
        $no_minggu++;
        $tanggal_sesi_show = tanggal_sesi_show($no_minggu, $awal_sesi, $jeda_sesi, 1);

        // sesi normal
        $no_sesi_harian++;
        $tr .= "
          <tr>
            <td class=tengah>$no_minggu</td>
            <td>$Minggu normal sesi $no_sesi_harian</td>
            <td>$tanggal_sesi_show</td>
          </tr>
        ";
      } // end for sesi normal

      // tambah row sesi tenang jika ada
      $minggu_tenang = $arr_musim['minggu_tenang'];
      if ($minggu_tenang) {
        for ($j = 1; $j <= $minggu_tenang; $j++) {
          $no_minggu++;
          $tanggal_sesi_show = tanggal_sesi_show($no_minggu, $awal_sesi, $jeda_sesi, 0);
          $tr .= "
          <tr class='tengah gradasi-abu abu f14 miring'>
            <td class=tengah>$no_minggu</td>
            <td colspan=100%>$Minggu tenang  | $tanggal_sesi_show</td>
          </tr>
        ";
        }
      }

      // tambah row ujian
      $durasi_ujian = $arr_musim['durasi_ujian'];
      if ($durasi_ujian) {
        $jenis_sesi = $musim == 'uts' ? 2 : 3;
        for ($j = 1; $j <= $durasi_ujian; $j++) {
          $no_minggu++;
          $tanggal_sesi_show = tanggal_sesi_show($no_minggu, $awal_sesi, $jeda_sesi, $jenis_sesi);
          $tr .= "
            <tr class='tengah gradasi-kuning biru miring'>
              <td>$no_minggu</td>
              <td colspan=100%>$nama_musim $Minggu ke-$j | $tanggal_sesi_show</td>
            </tr>
          ";
        }
      }
    } else {
      // tidak ada pra-$nama_musim
      die(div_alert('danger', "Tidak sesi untuk $nama_musim"));
    }
  }







  $inputs = "
    $inputs
    <h3 class=mt4>Tabel Estimasi Jadwal Sesi</h3>
    <p>Untuk seting Jadwal tiap sesi dapat dilakukan nanti pada Manage Sesi</p>
    <div class=wadah>
      <table class=table>
        <thead>
          <th class=tengah>No</th>
          <th>Sesi / Pertemuan</th>
          <th>Estimasi Awal Sesi (auto)</th>
        </thead>
        $tr
      </table>
    </div>
  ";

  // Closing Room
  $jam_sesi = date('H:i', strtotime($awal_sesi));
  $no_minggu_closed = $no_minggu + 2;
  $selisih = $jeda_sesi * ($no_minggu_closed - 1);
  $tanggal_close = date('Y-m-d H:i',  strtotime("+$selisih day", strtotime($awal_sesi)));
  $tgl_close = date('Y-m-d',  strtotime($tanggal_close));
  $tanggal_close_show = date('d-M-Y, H:i', strtotime($tanggal_close));

  $total_sesi = $d_room['minggu_normal_uts'] + $d_room['minggu_normal_uas'];

  $inputs .= "
    )* default Close Room adalah dua $Minggu setelah UAS.
    <hr>
    <input class='bg-yellow' type='hidden' name=tanggal_close id=tanggal_close value='$tanggal_close' required />
    <div class='mb1'>Tanggal Close Room:</div>
    <div class='flexy'>
      <div>
        <input class='form-control tanggal_close_trigger' type='date' id=tgl_tanggal_close value='$tgl_close' />
      </div>
      <div>
        <input class='form-control tanggal_close_trigger' type='time' id=jam_tanggal_close min='7:00' max='22:00' value='$jam_sesi' />
      </div>
    </div>
    
    <div class='mt2'>
      )* Sistem otomatis akan membuatkan sebanyak $no_minggu sesi untuk room ini.
    </div>
    
    <div class='mt2'>
      <label>
        <input type=checkbox required />
        Saya nanti akan melanjutkan penjadwalan untuk tiap sesi dan tiap Grup Kelas 
      </label>
    </div>
    
    <div class='mt2 mb4'>
      <label>
        <input type=checkbox required />
        Saya menyatakan bahwa pada tanggal diatas peserta tidak dapat lagi Update Activity (belajar) pada room ini, dan hanya dapat melihat History Activity saja.
      </label>
    </div>

    <script>
      $(function() {
        $('.tanggal_close_trigger').change(function() {
          $('#tanggal_close').val(
            $('#tgl_tanggal_close').val() +
            ' ' +
            $('#jam_tanggal_close').val()
          );
        })
      })
    </script>

  ";
}
