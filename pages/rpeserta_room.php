<?php
if (!isset($id_room) || !$id_room) die('<b class=red>rpeserta_room membutuhkan id_room</b>');
$s = "SELECT 
a.id,
a.war_image,
a.id as id_peserta,
a.nama as nama_peserta,
a.image,
c.kelas,
a.nama
FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE d.id_room = $id_room 
AND a.id_role = 1 -- mhs only
AND a.status = 1 -- mhs aktif 
AND d.ta = $ta_aktif 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rpeserta = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rpeserta[$d['id']] = $d;
}
