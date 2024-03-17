<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';
$abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];

$judul = 'Assign Soal';
set_title($judul);
echo "
  <h1>$judul</h1>
  <div class=mb2>Assign Soal-soal yang tersedia ke dalam Paket Soal</div>
";

















if (isset($_POST['btn_simpan_soal'])) {

  // clean SQL
  foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);



  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan data soal berhasil.');
  jsurl('', 2000);
  exit;
}






































# =============================================
# MAIN SELECT SOAL
# =============================================
$s = "SELECT a.*, 
a.id as id_soal,
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_soal=a.id) count_assign 
FROM tb_soal a 
WHERE a.id_room=$id_room 
AND tipe_soal='PG' 
ORDER BY date_created";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr = div_alert('danger', "Belum ada data soal untuk room ini.");
} else {
  $tr = '';
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;
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
      JOIN tb_paket_soal b ON a.id_paket_soal=b.id 
      WHERE a.id_soal=$id_soal";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $li = '';
      while ($d2 = mysqli_fetch_assoc($q2)) {
        $li .= "<li>$d2[nama_paket_soal]</li>";
      }
      $list_paket = "<ol>$li</ol>";
    }

    $tr .= "
      <tr class=tr_soal id=tr_soal__$id_soal>
        <td>$no</td>
        <td>
          <span id=kalimat_soal__$id_soal>$d[soal]</span>
          <div class='f12 abu'>$opsies</div>
        </td>
        <td width=20%>Assign</td>
      </tr>
    ";
  }
}
# ================================================ -->
# END MAIN SELECT SOAL
# ================================================ -->
$tb_soal = "
  <table class=table>
    <thead class=gradasi-toska>
      <th class=proper>no</th>
      <th class=proper>Kalimat Soal</th>
      <th class=proper>Ceklis Assign</th>
    </thead>
    $tr
  </table>
";

























# =============================================
# MAIN SELECT PAKET
# =============================================
$s = "SELECT a.*, 
a.id as id_paket,
a.nama as nama_paket,
(
  SELECT COUNT(1) FROM tb_assign_soal 
  WHERE id_paket=a.id) count_soal 
FROM tb_paket_soal a 
WHERE a.id_room=$id_room 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  $tr = div_alert('danger', "Belum ada data soal untuk room ini.");
} else {
  $tr = '';
  $no = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $no++;
    $id_paket = $d['id_paket'];

    $count_soal = $d['count_soal'];

    $tr .= "
      <tr class=tr_soal id=tr_soal__$id_paket>
        <td>$no</td>
        <td>
          <span id=nama_paket__$id_paket>$d[nama_paket]</span>
          <div class='f12 abu'>$d[kelas]</div>
        </td>
        <td width=20%>Assign</td>
      </tr>
    ";
  }
}
# ================================================ -->
# END MAIN SELECT PAKET
# ================================================ -->
$tb_paket = "
  <table class=table>
    <thead class=gradasi-toska>
      <th class=proper>no</th>
      <th class=proper>Paket Soal</th>
      <th class=proper>Ceklis Assign</th>
    </thead>
    $tr
  </table>
";
























# ================================================ -->
# FINAL ECHO ASSIGN DUA TABEL
# ================================================ -->
echo "
<div class=row>
  <div class=col-6>$tb_soal</div>
  <div class=col-6>$tb_paket</div>
</div>
";












?>
<script type="text/javascript">
  $(function() {
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