<?php
# ============================================================
# ALL PESERTA DI TA AKTIF
# ============================================================
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
JOIN tb_room e ON d.id_room=e.id 
WHERE 1 -- d.id_room = $ id_room 
AND a.id_role = 1 -- mhs only
AND a.status = 1 -- mhs aktif 
AND 1 -- d.ta = $ta_aktif 
AND c.ta = $ta_aktif -- Kelas Aktif
AND e.status = 100 -- Room Aktif
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$rpeserta = [];
while ($d = mysqli_fetch_assoc($q)) {
  $rpeserta[$d['id']] = $d;
}

// echo '<pre>';
// var_dump($rpeserta);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;
