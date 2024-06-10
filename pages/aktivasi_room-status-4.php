<?php
























# ============================================================
# AKTIVASI JADWAL SESI
# ============================================================
if (!$d_room['awal_sesi']) {
  die(div_alert('danger', "Data Room awal sesi belum ada."));
} else {
  $awal_sesi = $d_room['awal_sesi'];
  $awal_sesi_show = '<span class="tebal biru">' . $nama_hari[date('w', strtotime($awal_sesi))] . ', ' . date('F-d, Y, H:i', strtotime($awal_sesi)) . '</span> | ' . eta2($d_room['awal_sesi']);
  $inputs = "
    <div class='wadah bg-white flexy'>
      <div>
        Awal Sesi : $awal_sesi_show 
      </div>
      <div>
        <button name=reset_awal_sesi class='btn btn-danger btn-sm' onclick='return confirm(`Yakin untuk Reset Awal Sesi?`)'>Reset Awal Sesi</button>
      </div>
    </div>
  ";
  $no_sesi_harian = 0;
  $no_minggu = 0;
  $tr = '';
  if ($d_room['jumlah_sesi_uts']) {
    for ($i = 1; $i <= $d_room['jumlah_sesi_uts']; $i++) {
      $selisih = 7 * $no_minggu;
      $tanggal_sesi = date('Y-m-d H:i',  strtotime("+$selisih day", strtotime($awal_sesi)));
      $tanggal_sesi_show = date('d-M-Y, H:i', strtotime($tanggal_sesi));
      $no_minggu++;
      $UTS = '';
      if ($i == $d_room['jumlah_sesi_uts']) {
        // tambah row minggu tenang jika ada
        if ($d_room['minggu_tenang_uts']) {
          for ($j = 1; $j <= $d_room['minggu_tenang_uts']; $j++) {
            $tr .= "
            <tr class='tengah gradasi-abu abu f14 miring'>
              <td colspan=100%>minggu tenang  | $tanggal_sesi_show</td>
            </tr>
          ";
          }
        }

        // penanda UTS
        $UTS = '<span class="tebal biru miring">UTS</span>';

        // tambah row UTS
        if ($d_room['durasi_uts']) {
          for ($j = 1; $j <= $d_room['durasi_uts']; $j++) {
            $tr .= "
            <tr class='tengah gradasi-kuning biru miring'>
              <td colspan=100%>UTS Minggu ke-$j | $tanggal_sesi_show</td>
            </tr>
          ";
          }
        }
      } else {
        // pertemuan biasa UTS
        $no_sesi_harian++;
        $tr .= "
        <tr>
          <td>Pertemuan $no_sesi_harian $UTS</td>
          <td>$tanggal_sesi_show</td>
        </tr>
      ";
      }
    } // end for sesi UTS
  } else {
    // tidak ada pra-UTS
    die(div_alert('danger', 'Tidak sesi untuk UTS'));
  }

  if ($d_room['jumlah_sesi_uas']) {
    for ($i = 1; $i <= $d_room['jumlah_sesi_uas']; $i++) {
      $selisih = 7 * $no_minggu;
      $tanggal_sesi = date('Y-m-d H:i',  strtotime("+$selisih day", strtotime($awal_sesi)));
      $tanggal_sesi_show = date('d-M-Y, H:i', strtotime($tanggal_sesi));
      $no_minggu++;
      $UAS = '';
      if ($i == $d_room['jumlah_sesi_uas']) {
        // tambah row minggu tenang jika ada
        if ($d_room['minggu_tenang_uas']) {
          for ($j = 1; $j <= $d_room['minggu_tenang_uas']; $j++) {
            $tr .= "
            <tr class='tengah gradasi-abu abu f14 miring'>
              <td colspan=100%>minggu tenang | $tanggal_sesi_show</td>
            </tr>
          ";
          }
        }

        // penanda UAS
        $UAS = '<span class="tebal biru miring">UAS</span>';

        // tambah row UAS
        if ($d_room['durasi_uas']) {
          for ($j = 1; $j <= $d_room['durasi_uas']; $j++) {
            $tr .= "
            <tr class='tengah gradasi-kuning biru miring'>
              <td colspan=100%>UAS Minggu ke-$j | $tanggal_sesi_show</td>
            </tr>
          ";
          }
        }
      } else {
        // pertemuan biasa UAS
        $no_sesi_harian++;
        $tr .= "
        <tr>
          <td>Pertemuan $no_sesi_harian $UAS</td>
          <td>$tanggal_sesi_show</td>
        </tr>
      ";
      }
    } // end for sesi UAS

  } else {
    // tidak ada pra-UAS
    die(div_alert('danger', 'Tidak sesi untuk UAS'));
  }




  // minggu tenang

  $inputs = "
    $inputs
    <h3 class=mt4>Tabel Estimasi Jadwal Sesi</h3>
    <p>Untuk seting Jadwal tiap sesi dapat dilakukan nanti pada Manage Sesi</p>
    <div class=wadah>
      <table class=table>
        <thead>
          <th>Sesi / Pertemuan</th>
          <th>Tanggal Estimasi (auto)</th>
        </thead>
        $tr
      </table>
    </div>
  ";

  // Closing Room
  $jam_sesi = date('H:i', strtotime($awal_sesi));
  $no_minggu++;
  $no_minggu++;
  $selisih = 7 * $no_minggu;
  $tanggal_close = date('Y-m-d H:i',  strtotime("+$selisih day", strtotime($awal_sesi)));
  $tgl_close = date('Y-m-d',  strtotime($tanggal_close));
  $tanggal_close_show = date('d-M-Y, H:i', strtotime($tanggal_close));

  $total_sesi = $d_room['jumlah_sesi_uts'] + $d_room['jumlah_sesi_uas'];

  $inputs .= "
    )* default Close Room adalah seminggu setelah UAS.
    <hr>
    <input class='bg-yellow' type='hiddena' name=tanggal_close id=tanggal_close value='$tanggal_close' required />
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
      )* Sistem otomatis akan membuatkan sebanyak $total_sesi sesi untuk room ini.
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
