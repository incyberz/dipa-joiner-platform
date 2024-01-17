<?php 
if(!$id_room) die(erid('id_room'));
# ========================================================
# GET DATA ROOM
# ========================================================
$s = "SELECT * FROM tb_room a 
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

