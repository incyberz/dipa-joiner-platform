<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';
$abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
$get_id_paket = $_GET['id_paket'] ?? '';

$judul = 'Assign Soal';
set_h2($judul, "Silahkan cek atau cek-all kemudian klik Assign untuk memasukan soal ke Paket", '?manage_paket_soal');

















if (isset($_POST['btn_assign']) || isset($_POST['btn_drop'])) {
  if (isset($_POST['id_soal'])) {
    $id_paket = $_POST['btn_assign'] ?? $_POST['btn_drop'];
    foreach ($_POST['id_soal'] as $id_soal) {
      if (isset($_POST['btn_drop'])) {
        // proses drop
        $s = "DELETE FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        echolog("Dropping id_soal: $id_soal");
      } else {
        // proses assign
        $s = "SELECT 1 FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        if (mysqli_num_rows($q)) {
          echolog('Soal sudah ter-assign.');
        } else {
          $s = "INSERT INTO tb_assign_soal (id_soal,id_paket) VALUES ($id_soal,$id_paket)";
          $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
          echolog("Assigning id_soal: $id_soal");
        }
      }
    }
  }

  echo div_alert('success', 'Assign soal berhasil.');
  jsurl('', 2000);
  exit;
}






































# =============================================
# PAKET SOAL PROPERTIES
# =============================================
$s = "SELECT a.nama as nama_paket_soal,
(
  SELECT COUNT(1) 
  FROM tb_jawabans p 
  JOIN tb_paket_kelas q ON p.id_paket_kelas=q.paket_kelas  
  WHERE q.id_paket=a.id) count_penjawab 

FROM tb_paket a WHERE a.id=$get_id_paket";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die('Data Paket Soal tidak ditemukan.');
$d = mysqli_fetch_assoc($q);
$nama_paket_soal = $d['nama_paket_soal'];
$count_penjawab = $d['count_penjawab'];

if ($count_penjawab) echo div_alert('danger', "Perhatian! Paket ini sudah dijawab oleh <a target=_blank href='?monitoring_ujian&id_paket=$get_id_paket'>$count_penjawab peserta</a>. Anda tidak dapat lagi melakukan proses Assign ataupun Drop Soal dari Paket Soal ini.");
$disabled_assign = $count_penjawab ? 'disabled' : '';

# =============================================
# MAIN SELECT SOAL
# =============================================
$s = "SELECT a.*, 
a.id as id_soal,
(
  SELECT 1 FROM tb_assign_soal 
  WHERE id_soal=a.id
  AND id_paket='$get_id_paket') sudah_assign, 
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_soal=a.id) count_assign 
FROM tb_soal a 
WHERE a.id_room=$id_room 
AND tipe_soal='PG' 
ORDER BY date_created";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) {
  $tr = div_alert('danger', "Belum ada data soal untuk room ini.");
} else {
  $tr = '';
  $tr_assigned = '';
  $no = 0;
  $no_kiri = 0;
  $no_kanan = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $id_soal = $d['id_soal'];
    $arr = explode('~~~', $d['opsies']);
    $opsies = '';
    foreach ($arr as $key => $value) {
      $opsies .= "<span class='' style='display:inline-block;margin-right:15px'>$abjad[$key]. <span id=opsi_$abjad[$key]__$id_soal>$value</span></span>";
    }

    $pembahasan_show = $d['pembahasan'] ? "<div class='miring abu f14' id=pembahasan__$id_soal>$d[pembahasan]</div>" : $null;

    $count_assign = $d['count_assign'];
    $list_paket = $null;
    if ($count_assign) {
      $s2 = "SELECT b.nama as nama_paket_soal 
      FROM tb_assign_soal a 
      JOIN tb_paket b ON a.id_paket=b.id 
      WHERE a.id_soal=$id_soal";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $li = '';
      while ($d2 = mysqli_fetch_assoc($q2)) {
        $li .= "<li>$d2[nama_paket_soal]</li>";
      }
      $list_paket = "<ol>$li</ol>";
    }

    if ($d['sudah_assign']) {
      $no_kanan++;
      $class_input = 'drop_soal';
      $no = $no_kanan;
    } else {
      $no_kiri++;
      $class_input = 'cek_soal';
      $no = $no_kiri;
    }

    $this_tr = "
      <tr class=tr_soal id=tr_soal__$id_soal>
        <td>$no</td>
        <td>
          <span id=kalimat_soal__$id_soal>$d[soal]</span>
          <div class='f12 abu'>$opsies</div>
        </td>
        <td width=10% class=tengah>
          <input class=$class_input type=checkbox name='id_soal[]' value=$id_soal $disabled_assign>
        </td>
      </tr>
    ";

    if (!$d['sudah_assign']) {
      $tr .= $this_tr;
    } else {
      $tr_assigned .= $this_tr;
    }
  } // end while
  $tr = $tr ? $tr : tr_col('Soal tidak ada atau semua sudah di di-assign ke Paket Soal.', 'p4 gradasi-kuning consolas f12 miring abu');
}
# ================================================ -->
# FINAL ECHO
# ================================================ -->
echo "
<h2 class='bordered br5 f20 consolas tengah p2 gradasi-toska'>
  Assign Soal untuk Paket <b class='darkblue'>$nama_paket_soal</b>
</h2>
<div class=row>
  <div class=col-6>
    <form method=post>
      <table class=table>
        <thead class=gradasi-toska>
          <th class=proper>no</th>
          <th class=proper>Soal yang Tersedia</th>
          <th class='proper tengah'>
            <input type=checkbox id=toggle_cekall $disabled_assign>
            <div class='f12 mt1'>Cek All</div>
          </th>
        </thead>
        $tr
      </table>
      <button class='btn btn-primary w-100' value=$get_id_paket name=btn_assign $disabled_assign>Assign</button>
    </form>  
  </div>
  <div class=col-6>
    <form method=post>
      <table class=table>
        <thead class=gradasi-toska>
          <th class=proper>no</th>
          <th class=proper>Soal yang ada di Paket</th>
          <th class='proper tengah'>
            <input type=checkbox id=toggle_dropall $disabled_assign>
            <div class='f12 mt1'>Drop All</div>
          </th>
        </thead>
        $tr_assigned
      </table>
      <button class='btn btn-danger w-100' value=$get_id_paket name=btn_drop $disabled_assign>Drop</button>
    </form>
  </div>
</div>
";




































?>
<script type="text/javascript">
  $(function() {
    $('#toggle_cekall').click(function() {
      $('.cek_soal').prop('checked', $(this).prop('checked'));
    });
    $('#toggle_dropall').click(function() {
      $('.drop_soal').prop('checked', $(this).prop('checked'));
    });
    $('.radio_gambar').click(function() {
      let val = $(this).val();
      if (val) {
        $('#gambar_soal').prop('disabled', 1);
      } else {
        $('#gambar_soal').prop('disabled', 0);
      }
    })
  })
</script>