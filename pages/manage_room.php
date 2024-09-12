<?php
instruktur_only();
$judul = 'Manage Room';
set_h2($judul);

# ============================================================
# PROCESSORS
# ============================================================
if (isset($_POST['btn_save_room'])) {
  unset($_POST['btn_save_room']);

  $pairs = '';
  foreach ($_POST as $key => $value) {
    $value  = addslashes($value);
    $value_quote = $value ? "'$value'" : 'NULL';
    $pairs .= "$key=$value_quote,";
  }
  $pairs .= '__';
  $pairs = str_replace(',__', '', $pairs);

  $s = "UPDATE tb_room SET $pairs WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Update room sukses.");
  jsurl('', 2000);
}

$s = "SELECT 
a.nama as nama_room,
a.singkatan,
b.nama as room_owner,
c.nama as status_room,
a.ta,
a.lembaga,
a.awal_sesi,
a.tanggal_close as auto_close_pada,
a.durasi_tatap_muka as menit_tatap_muka

FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id 
JOIN tb_status_room c ON a.status=c.status  
WHERE a.id='$id_room'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    foreach ($d as $key => $value) {
      if (
        $key == 'id'
        || $key == 'date_created'
      ) {
        continue;
      } elseif ($key == 'awal_sesi' || $key == 'auto_close_pada') {
        $value = hari_tanggal($value) . ' | ' . eta2($value);
      } elseif ($key == 'status_room') {
        $value = $value == 'Active' ? "<span class=green>Active $img_check</span>" : "$value $img_warning";
      } elseif ($key == 'ta') {
        $value = tahun_ajar_show($value);
      } elseif ($key == 'nama_room') {
        $value = "<input required minlength=5 maxlength=30 name=nama value='$value' class='form-control'>";
      } elseif ($key == 'singkatan') {
        $value = "<input required minlength=3 maxlength=10 name=$key value='$value' class='form-control'>";
      } elseif ($key == 'lembaga') {
        $value = "<input required minlength=5 maxlength=50 name=$key value='$value' class='form-control'>";
      }


      $kolom = key2kolom($key);
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
}

$tb = $tr ? "
  <table class='table gradasi-hijau td_trans'>
    $tr
  </table>
" : div_alert('danger', "Data room tidak ditemukan.");
echo "
  <form method=post>
    $tb
    <button class='btn btn-primary' name=btn_save_room>Save Room</button>
  </form>

";

?>
<hr>
<div class="wadah">
  <a href="?destroy_room" class="btn btn-danger" onclick='return confirm(`Destroy Room?`)'>Destroy Room</a>

  <div class="alert alert-danger mt2">Fitur ini masih dalam tahap pengembangan. Terimakasih sudah mencoba.</div>

</div>