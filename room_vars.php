<?php 
if(!$id_room) die(erid('id_room'));

# ========================================================
# GET DATA ROOM
# ========================================================
$s = "SELECT a.*, b.nama as instruktur,
(SELECT id FROM tb_room_kelas WHERE kelas='$kelas' AND id_room='$id_room') id_room_kelas 
FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id 
WHERE a.id=$id_room 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

if(!mysqli_num_rows($q)){
  unset($_SESSION['dipa_id_room']);
  die(div_alert('danger','Room tidak ditemukan.'));
} 

$d = mysqli_fetch_assoc($q);
$room = $d['singkatan'];
$nama_room = $d['nama'];
$status_room = $d['status'];
$id_room_kelas = $d['id_room_kelas'];
$instruktur = $d['instruktur'];

if(!$id_room_kelas){
  $pesan = $id_role==2 ? "<a href='?assign_room_kelas'>Assign Room Kelas</a>" : "Segera hubungi instruktur ($instruktur)!";
  if($dm) $pesan = "<a target=_blank href='?assign_room_kelas'>Assign Room Kelas</a>" ;
  if($parameter!='assign_room_kelas'){
    die(div_alert('danger',"Kelas <u>$kelas</u> belum di-assign ke room <u>$room</u>. $pesan"));
  }
}
