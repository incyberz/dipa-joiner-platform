<?php
# =====================================================
# PROCESSOR PILIH ROOM
# =====================================================
if (isset($_POST['btn_pilih'])) {
  $_SESSION['dipa_id_room'] = $_POST['btn_pilih'];
  jsurl('?');
}
























# ============================================================
# MAIN SELECT ROOM
# ============================================================
$s = "SELECT a.*,
a.nama as room,
a.status as status_room,
a.id as id_room,
b.nama as creator,
b.id as id_creator,
(
  SELECT 1  
  FROM tb_room p 
  WHERE p.created_by = $id_peserta 
  AND p.id=a.id) my_room 

FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id  
WHERE 1 -- b.id = $id_peserta
ORDER BY my_room DESC, a.status DESC
";

// echo '<pre>';
// var_dump($s);
// echo '</pre>';


$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$my_room = '';
$other_room = '';
while ($d = mysqli_fetch_assoc($q)) {
  if ($d['status_room'] == 100) {
    $status = 'Aktif';
    $gradasi = 'hijau';
    $btn = "<button class='btn btn-success mt2 w-100' name=btn_pilih value=$d[id_room]>Pilih Room</button>";
  } elseif ($d['status_room'] < 0) {
    $status = 'Closed';
    $gradasi = 'kuning';
    $btn = "<button class='btn btn-warning mt2 w-100' name=btn_pilih value=$d[id_room] onclick='return confirm(\"Pilih Closed Room untuk melihat history?\")'>Closed</button>";
  } else {
    $status = 'Belum Aktif';
    $gradasi = 'merah';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(\"Room belum diaktifkan oleh Instruktur. Segera hubungi beliau via whatsApp!\")'>Inactive</span>";
    $btn = "<a href='?pilih_room&aktivasi_room=$d[id_room]' class='btn btn-secondary mt2 w-100' >Aktivasi</a>";
  }

  if ($id_room == $d['id_room']) {
    $wadah_active = 'wadah_active';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(\"Kamu sedang berada di room ini.\")'>Selected</span>";
  } else {
    $wadah_active = '';
  }

  if ($d['my_room']) {
    $border = 'solid 3px blue';
  } else {
    $gradasi = 'abu';
    $border = '';
    $btn = "<a href='?pilih_room&daftar_ke_room=$d[id_room]' class='btn btn-secondary mt2 w-100' value=$d[id_room]>Daftar Anggota</a>";
  }

  $room = "
    <div class='col-md-4 col-lg-3'>
      <div class='wadah $wadah_active gradasi-$gradasi tengah' style='border: $border;'>
        <div class='darkblue f18'>$d[room]</div>
        <div class=f12>Status: $status</div>
        <img src='$lokasi_profil/peserta-$d[id_creator].jpg' alt='pengajar' class='foto_profil'>
        <div>By: $d[creator]</div>
        $btn
      </div>
    </div>
  ";
  if ($d['my_room']) {
    $my_room .= $room;
  } else {
    $other_room .= $room;
  }
}

echo "
<div class=container>
  <form method=post>
    <div class=row>
      $my_room
    </div>
    <hr>
    <div class='tengah'>
      <div class='mb2'>
        <a class='btn btn-success' href='?buat_room' onclick='return confirm(`Buat Room Baru?`)'>Buat Room Baru</a>
      </div>
      <a href='?logout' onclick='return confirm(`Logout?`)'>Logout</a>
    </div>
    <hr>
    <h3 class='mt4 mb4 tengah'>Other Rooms</h3>
    <div class=row>
      $other_room
    </div>
  </form>
</div>
";
