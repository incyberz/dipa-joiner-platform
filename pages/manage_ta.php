<?php
mulai($parameter);
$get_ta = $_GET['ta'] ?? '';

// include 'manage_ta-processors.php';

if (!$get_ta) {
  set_h2('manage ta');

  $rfield = [
    'awal_kuliah' => [
      '1' => '-9-1', // ganjil awal september
      '2' => '-2-1', // genap awal februari
    ],
    'akhir_kuliah' => [
      '1' => 'auto +20 week',
      '2' => 'auto +20 week',
    ],
    'senin_pertama_kuliah' => [
      '1' => 'auto Senin awal kuliah',
      '2' => 'auto Senin awal kuliah',
    ],
    'awal_sekolah' => [
      '1' => '-7-1', // awal sekolah ganjil Juli
      '2' => '-1-1', // awal sekolah genap Januari
    ],
    'akhir_sekolah' => [
      '1' => 'auto +20 week', // akhir_sekolah ganjil Januari
      '2' => 'auto +15 week', // akhir_sekolah genap Januari
    ],
    'senin_pertama_sekolah' => [
      '1' => 'auto Senin awal_sekolah',
      '2' => 'auto Senin awal_sekolah',
    ]
  ];

  $s = "SELECT a.* 
  FROM tb_ta a WHERE 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  if (mysqli_num_rows($q)) {
    $i = 0;
    $th = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $i++;
      $td = '';
      foreach ($d as $key => $value) {

        # ============================================================
        # AUTOFILL DATA DEFAULT
        # ============================================================
        $thn = substr($d['ta'], 0, 4);
        $gg = substr($d['ta'], 4, 1);
        $awal_kuliah = $gg == 1 ? $thn . $rfield['awal_kuliah'][$gg] : ($thn + 1) . $rfield['awal_kuliah'][$gg];
        $senin_pertama_sekolah = $gg == 1 ? $thn . $rfield['awal_sekolah'][$gg] : ($thn + 1) . $rfield['awal_sekolah'][$gg];

        # ============================================================
        # CARI HARI SENIN AWAL KULIAH
        # ============================================================
        $w = date('w', strtotime($awal_kuliah));
        if ($w == 1) { // Jika pas hari Senin
          $senin_pertama_kuliah = $awal_kuliah;
        } else {
          // Ambil hari Senin berikutnya
          $jeda = (8 - $w) % 7; // Hitung selisih hari ke Senin berikutnya
          $senin_pertama_kuliah = date('Y-m-d', strtotime("+$jeda day", strtotime($awal_kuliah)));
        }

        # ============================================================
        # CARI HARI SENIN AWAL SEKOLAH
        # ============================================================
        $w = date('w', strtotime($senin_pertama_sekolah));
        if ($w == 1) { // Jika pas hari Senin
          $senin_pertama_sekolah = $senin_pertama_sekolah;
        } else {
          // Ambil hari Senin berikutnya
          $jeda = (8 - $w) % 7; // Hitung selisih hari ke Senin berikutnya
          $senin_pertama_sekolah = date('Y-m-d', strtotime("+$jeda day", strtotime($senin_pertama_sekolah)));
        }

        if ($key == 'nama' and !$value) {
          $s2 = "UPDATE tb_ta SET nama=ta WHERE ta=$d[ta]";
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
        } elseif ($key == 'status') {
          // do nothing
        } elseif (!$value) {
          if ($key == 'akhir_kuliah') {
            // akhir_kuliah +20 week
            $senin_akhir = date('Y-m-d', strtotime("+20 week", strtotime($senin_pertama_kuliah)));
            $sabtu_akhir = date('Y-m-d', strtotime("-2 day", strtotime($senin_akhir)));
            $new_value = $sabtu_akhir;
          } elseif ($key == 'akhir_sekolah') {
            // akhir_sekolah ganjil +20 week
            // akhir_sekolah genap +15 week
            $jeda =  $gg == '2' ? 15 : 20;
            $senin_akhir = date('Y-m-d', strtotime("+$jeda week", strtotime($senin_pertama_sekolah)));
            $sabtu_akhir = date('Y-m-d', strtotime("-2 day", strtotime($senin_akhir)));
            $new_value = $sabtu_akhir;
          } elseif ($key == 'senin_pertama_kuliah') { // cari hari senin
            $new_value = $senin_pertama_kuliah;
          } elseif ($key == 'senin_pertama_sekolah') { // cari hari senin
            $new_value = $senin_pertama_sekolah;
          } else {
            echo '<pre>';
            var_dump("Key [$key] belum auto-hitung.");
            echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
            exit;
          }
          $s2 = "UPDATE tb_ta SET $key='$new_value' WHERE ta=$d[ta]";
          // echolog($s2);
          $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
          $value = $new_value;
        }


        if (
          $key == 'id'
          || $key == 'date_created'
        ) continue;
        if ($i == 1) {
          $kolom = key2kolom($key);
          $th .= "<th>$kolom</th>";
        }
        $editable = key_exists($key, $rfield) ? 'editable' : '';
        $td .= "<td class='$editable' id=ta__$key" . "__$d[ta]>$value</td>";
      }
      $tr .= "
        <tr>
          $td
        </tr>
      ";
    }
  }

  $tb = $tr ? "
    <table class=table>
      <thead>$th</thead>
      $tr
    </table>
  " : div_alert('danger', "Data ta tidak ditemukan.");
  echo "$tb";
}
?>
<script>
  $(function() {
    $('.editable').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let tb = rid[0];
      let field_target = rid[1];
      let field_id_value = rid[2];
      let isi_baru = prompt('New value:', $(this).text());
      console.log(isi_baru);

      if (isi_baru === null) return;
      if (isi_baru === '') {
        let y = confirm('Set null value (kosongkan nilai)?');
        if (!y) return;
        isi_baru = 'null';
      }
      $.ajax({
        url: `ajax/ajax_crud.php?tb=ta&aksi=ubah&field_id_value=${field_id_value}&field_id=ta&field_target=${field_target}&isi_baru=${isi_baru}`,
        success: function(a) {
          alert(a)
        }
      })
    })
  })
</script>