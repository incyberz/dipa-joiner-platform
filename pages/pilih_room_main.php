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
if ($id_role == 1) {
  // $Room kelas _peserta
  $sub_sql_my_room = "SELECT 1  
  FROM tb_room_kelas p 
  JOIN tb_kelas q ON p.kelas=q.kelas  
  JOIN tb_kelas_peserta r ON q.kelas=r.kelas  
  WHERE p.id_room=a.id
  AND q.kelas='$kelas'
  AND r.id_peserta = $id_peserta";
} else {
  // $Room owner
  $sub_sql_my_room = "SELECT 1  
  FROM tb_room p 
  WHERE p.created_by = $id_peserta 
  AND p.id=a.id 
  ";
}

$s = "SELECT a.*,
a.nama as room,
a.status as status_room,
a.id as id_room,
b.nama as creator,
b.id as id_creator,
b.war_image as war_image_creator,
($sub_sql_my_room) my_room 

FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id  
WHERE 1 -- b.id = $id_peserta 
ORDER BY my_room DESC, a.jenjang,a.jenis,a.no, a.status DESC, a.nama  
LIMIT 50
";






$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$my_rooms = '';
$my_rooms_sd = [];
$my_rooms_sd[1] = ''; // mapel inti
$my_rooms_sd[2] = ''; // mapel tambahan
$my_rooms_sd[3] = ''; // mapel informal
$other_room = '';
while ($d = mysqli_fetch_assoc($q)) {
  if ($d['status_room'] == 100) {
    $status = 'Aktif';
    $gradasi = 'hijau';
    $btn = "<button class='btn btn-success mt2 w-100' name=btn_pilih value=$d[id_room]>Pilih $Room</button>";
  } elseif ($d['status_room'] < 0) {
    $status = 'Closed';
    $gradasi = 'kuning';
    $btn = "<button class='btn btn-warning mt2 w-100' name=btn_pilih value=$d[id_room] onclick='return confirm(`Pilih Closed $Room untuk melihat history?`)'>Closed</button>";
  } else {
    $status = 'Belum Aktif';
    $gradasi = 'merah';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(`$Room belum diaktifkan oleh $Trainer. Segera hubungi beliau via whatsApp!`)'>Inactive</span>";
    $btn = "<a href='?pilih_room&aktivasi_room=$d[id_room]' class='btn btn-secondary mt2 w-100' >Aktivasi</a>";
  }

  if ($id_room == $d['id_room']) {
    $wadah_active = 'wadah_active';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(`Kamu sedang berada di $Room ini.`)'>Selected</span>";
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

  $singkatan_room = "
    <div class='col-md-4 col-lg-3'>
      <div class='wadah $wadah_active gradasi-$gradasi tengah' style='border: $border;'>
        <div class='darkblue f18'>$d[room]</div>
        <div class=f12>Status: $status</div>
        <img src='$lokasi_profil/$d[war_image_creator]' alt='pengajar' class='foto_profil'>
        <div>By: $d[creator]</div>
        $btn
      </div>
    </div>
  ";
  if ($d['my_room']) {
    if ($d['jenjang'] == 'SD') {
      $my_rooms_sd[$d['jenis']] .= $singkatan_room;
    } else {
      $my_rooms .= $singkatan_room;
    }
  } else {
    $other_room .= $singkatan_room;
  }
}
$my_rooms = $my_rooms ? $my_rooms : div_alert('warning tengah', $id_role == 2 ? "Anda belum punya $Room di TA $ta_show" : "Kamu belum dimasukan ke $Room manapun pada TA. $ta_show");
$link_buat_room_baru = $id_role == 2 ?  "<div class='alert alert-info'>$Room digunakan untuk mewadahi kegiatan belajar Anda dengan <b>multiple-kelas</b> dan dapat dipakai kembali (<b>reusable</b>) di setiap Tahun Ajar.</div> <div class='mb2'><a class='btn btn-primary w-100 ' href='?buat_room' onclick='return confirm(`Buat $Room Baru?`)'>Buat $Room Baru</a></div>" : '';

$my_rooms_sd = (!$my_rooms_sd || $id_role != 2) ? '' : "
  <hr>
  <div class='wadah gradasi-toska'>
    <h4 class='tengah darkblue'>$Room Sekolah Dasar</h4>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Inti</div>
      <div class=row>
        $my_rooms_sd[1]
      </div>
    </div>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Tambahan</div>
      <div class=row>
        $my_rooms_sd[2]
      </div>
    </div>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Informal</div>
      <div class=row>
        $my_rooms_sd[3]
      </div>
    </div>
  </div>
";

# ============================================================
# FINAL ECHO
# ============================================================
echo "
<div class=container>
  <form method=post>
    <hr>
    <h3 class='darkblue f20 upper tengah mb4'>$Room Aktif $ta_show</h3>
    <div class=row>
      $my_rooms
    </div>
    $my_rooms_sd
    <hr>
    <div class='tengah'>
      $link_buat_room_baru
      <a href='?logout' onclick='return confirm(`Logout?`)'>Logout</a>
    </div>
    <hr>
    <h3 class='mt4 mb4 tengah'>$Room $Trainer lain</h3>
    <div class=row>
      $other_room
    </div>
  </form>
</div>
";
