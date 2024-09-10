<?php
# ============================================================
# SELECT ID_ROOM_KELAS NON INSTRUKTUR
# ============================================================
if (!$id_sesi_aktif) die('$id_sesi_aktif undefined.');
$s = "SELECT *,a.id as id_room_kelas_peserta ,
(
  SELECT COUNT(1) 
  FROM tb_kelas_peserta p 
  JOIN tb_peserta q ON p.id_peserta=q.id  
  WHERE p.kelas=a.kelas 
  AND q.status=1 -- hanya peserta aktif
  AND q.id_role=1 -- hanya peserta
  ) count_peserta_kelas, 
(
  SELECT COUNT(1) 
  FROM tb_presensi p 
  JOIN tb_peserta q ON p.id_peserta=q.id -- hanya peserta di room kelas TA sekarang 
  JOIN tb_kelas_peserta r ON q.id=r.id_peserta 
  JOIN tb_kelas s ON r.kelas=s.kelas 
  JOIN tb_room_kelas t ON s.kelas=t.kelas 

  WHERE p.id_sesi=$id_sesi_aktif 
  AND t.ta = $ta 
  AND q.id_role = 1 -- hanya peserta
  AND q.status = 1 -- hanya peserta aktif
  ) count_presenter
FROM tb_room_kelas a 
WHERE a.id_room=$id_room 
AND a.kelas != 'INSTRUKTUR' 
AND a.ta=$ta
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_count_peserta_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $satu_kelas_peserta = $d['kelas'];
  $satu_id_room_kelas_peserta = $d['id_room_kelas_peserta'];
  $arr_count_peserta_kelas .= "$d[kelas]=$d[count_presenter]=$d[count_peserta_kelas];";
}


# ============================================================
# DESCRIBE ROOM_COUNT
# ============================================================
$s = "DESCRIBE tb_room_count";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$koloms = [];
while ($d = mysqli_fetch_assoc($q)) {
  if ($d['Field'] == 'id_room' || $d['Field'] == 'last_update') continue;
  array_push($koloms, $d['Field']);
}

// cari apakah sudah UTS atau sudah UAS
$and_sudah_ujian = "AND id_room=$id_room AND awal_presensi <= '$now' limit 1";

$s = "SELECT 
(
  SELECT COUNT(1) 
  FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas 
  JOIN tb_room_kelas r ON q.kelas=r.kelas 
  JOIN tb_peserta s ON p.id_peserta=s.id  
  WHERE r.id_room=$id_room 
  AND q.status=1 -- hanya kelas aktif
  AND s.status=1 -- hanya peserta aktif
  AND s.id_role=1 -- hanya peserta
  AND r.ta=$ta -- hanya di TA sekarang
  ) count_peserta,
(
  SELECT COUNT(1) 
  FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas 
  JOIN tb_room_kelas r ON q.kelas=r.kelas 
  JOIN tb_peserta s ON p.id_peserta=s.id  
  WHERE r.id_room=$id_room 
  AND q.status=1 -- hanya kelas aktif
  AND s.status=1 -- hanya peserta aktif
  AND s.id_role=1 -- hanya peserta
  AND r.ta=$ta -- hanya di TA sekarang
  AND s.image is not null 
  AND s.profil_ok = 1  
  ) count_peserta_image_ok,
(
  SELECT COUNT(1) 
  FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas 
  JOIN tb_room_kelas r ON q.kelas=r.kelas 
  JOIN tb_peserta s ON p.id_peserta=s.id  
  WHERE r.id_room=$id_room 
  AND q.status=1 -- hanya kelas aktif
  AND s.status=1 -- hanya peserta aktif
  AND s.id_role=1 -- hanya peserta
  AND r.ta=$ta -- hanya di TA sekarang
  AND s.war_image is not null 
  ) count_peserta_war_image_ok,

(
  SELECT COUNT(1) 
  FROM tb_assign_latihan 
  WHERE id_room_kelas='$satu_id_room_kelas_peserta' 
  ) count_latihan, -- All TA
(
  SELECT COUNT(1) 
  FROM tb_assign_latihan 
  WHERE id_room_kelas='$satu_id_room_kelas_peserta' 
  AND is_wajib is not null) count_latihan_wajib, -- All TA

(
  SELECT COUNT(1) 
  FROM tb_assign_challenge 
  WHERE id_room_kelas='$satu_id_room_kelas_peserta') count_challenge, -- All TA
(
  SELECT COUNT(1) 
  FROM tb_assign_challenge 
  WHERE id_room_kelas='$satu_id_room_kelas_peserta' 
  AND is_wajib is not null) count_challenge_wajib, -- All TA
(
  SELECT COUNT(1) 
  FROM tb_paket_kelas 
  WHERE kelas='$satu_kelas_peserta' 
  ) count_ujian, -- All TA
(
  SELECT COUNT(1) 
  FROM tb_room_kelas   
  WHERE id_room=$id_room 
  AND ta=$ta 
  AND kelas != 'INSTRUKTUR') count_kelas, -- @TA Aktif
(
  SELECT COUNT(1) 
  FROM tb_sesi   
  WHERE id_room=$id_room 
  AND jenis = 1 -- sesi normal 
  AND awal_presensi <= '$now'
  ) count_presensi_aktif, -- @Updated Room
(
  SELECT COUNT(1) 
  FROM tb_sesi   
  WHERE id_room=$id_room 
  AND jenis = 1 -- sesi normal
  ) count_presensi, -- @Updated Room
( SELECT 1 FROM tb_sesi WHERE jenis=2 $and_sudah_ujian) sudah_uts, -- @Updated Room
( SELECT 1 FROM tb_sesi WHERE jenis=3 $and_sudah_ujian) sudah_uas, -- @Updated Room
( 
  SELECT count(1) FROM tb_bertanya p 
  JOIN tb_room_kelas q ON p.id_room_kelas=q.id 
  WHERE q.id_room=$id_room
  AND q.ta=$ta) count_bertanya, -- @TA Aktif
( 
  SELECT count(1) FROM tb_bertanya p 
  JOIN tb_room_kelas q ON p.id_room_kelas=q.id 
  WHERE q.id_room=$id_room
  AND p.verif_by is not null
  AND q.ta=$ta) count_bertanya_verified,
( 
  SELECT count(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
  JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
  WHERE r.id_room=$id_room
  AND r.ta=$ta) count_bukti_latihan,
( 
  SELECT count(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
  JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
  WHERE r.id_room=$id_room 
  AND verified_by IS NOT NULL
  AND r.ta=$ta) count_bukti_latihan_verified,
( 
  SELECT count(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
  JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
  WHERE r.id_room=$id_room
  AND r.ta=$ta) count_bukti_challenge,
( 
  SELECT count(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
  JOIN tb_room_kelas r ON q.id_room_kelas=r.id 
  WHERE r.id_room=$id_room 
  AND verified_by IS NOT NULL
  AND r.ta=$ta) count_bukti_challenge_verified

";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$room_count = mysqli_fetch_assoc($q);

$room_count['arr_count_peserta_kelas'] = $arr_count_peserta_kelas;


$pairs = '';
foreach ($koloms as $v) {
  if (key_exists($v, $room_count)) {
    $koma = $pairs ? ',' : '';
    $vnull = ($room_count[$v] === null || $room_count[$v] === '') ? 'NULL' : "'$room_count[$v]'";
    $pairs .= "$koma$v = $vnull";
  } else {
    echo "<div class='red bold'> [$v] belum dihitung.</h1>";
    exit;
  }
}



$s = "UPDATE tb_room_count SET $pairs, last_update=CURRENT_TIMESTAMP WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
// exit;
jsurl('?');
