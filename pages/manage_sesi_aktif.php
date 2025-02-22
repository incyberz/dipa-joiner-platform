<?php
if ($id_role == 1) die(div_alert('danger', "Silahkan hubungi $Trainer untuk Manage Sesi Aktif"));

if (isset($_POST['btn_cancel_room'])) {
  unset($_SESSION['dipa_id_room']);
  jsurl();
} elseif (isset($_POST['btn_close_room'])) {
  $s = "UPDATE tb_room SET status = NULL WHERE id = $id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  unset($_SESSION['dipa_id_room']);
  jsurl();
}

$s = "SELECT * FROM tb_sesi WHERE id_room=$id_room ORDER BY no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $awal_presensi = date('d M Y', strtotime($d['awal_presensi']));
  $akhir_presensi = date('d M Y', strtotime($d['akhir_presensi']));
  $tr .= "
    <tr id=tr__$d[id]>
      <td>$i</td>
      <td>$d[nama]</td>
      <td>$awal_presensi</td>
      <td>$akhir_presensi</td>
      <td>
        <button class='btn btn-primary ondev' onclick='alert(`On development.`)' ZZZ>Set Sesi Aktif</button>
      </td>
    </tr>
  ";
}

$hari = $nama_hari[$weekday];
$tanggal = date("d M Y");
$perhatian = $sesi_aktif ? '' : "
  <form method=post class='mt2 wadah gradasi-kuning' >
    <div class=' mb2'>Jika $Room ini sudah tidak terpakai di TA $ta_aktif, silahkan Close Room.</div>
    <button class='btn btn-primary' name=btn_close_room onclick='return confirm(`Close $Room ?`)'>Close $Room</button>
    <button class='btn btn-secondary' name=btn_cancel_room>Cancel</button>
  </form>
  <hr>
  <div class='blue mb2'>Jika $Room ini terpakai di TA $ta_aktif, silahkan klik Set Sesi Aktif.</div>
";
set_h2("Manage Sesi Aktif", "Saat ini hari $hari, $tanggal $perhatian");

echo "
  <table class=table>
    <thead>
      <th>No</th>
      <th>Sesi</th>
      <th>Awal Presensi</th>
      <th>Akhir Presensi</th>
    </thead>
    $tr
  </table>";
