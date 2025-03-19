<?php
$arr_act = ['latihan', 'challenge']; // paket soal for quiz
$arr_data_act = [];
if ($target_kelas) {
  foreach ($arr_act as $act) {
    $s = "SELECT a.id, a.id_sesi,
    b.nama as nama_act,
    b.ket 
    FROM tb_assign_$act a
    JOIN tb_$act b ON a.id_$act=b.id 
    JOIN tb_room_kelas c ON a.id_room_kelas=c.id 
    WHERE c.kelas = '$target_kelas' 
    -- AND a.id_room_kelas='$id_room_kelas' 
  
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    while ($d = mysqli_fetch_assoc($q)) {
      if (isset($arr_data_act[$act][$d['id_sesi']])) {
        array_push($arr_data_act[$act][$d['id_sesi']], $d);
      } else {
        $arr_data_act[$act][$d['id_sesi']][0] = $d;
      }
    }
  }
}

$arr_latihan = $arr_data_act['latihan'] ?? [];
$arr_challenge = $arr_data_act['challenge'] ?? [];

# ============================================================
# GET DATA PAKET
# ============================================================
$act = 'paket';
$s = "SELECT a.id, a.id_sesi,
  a.nama as nama_act,
  a.ket,
  (
    SELECT MAX(p.nilai) FROM tb_jawabans p
    JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas 
    WHERE q.id_paket=a.id  
    AND p.id_peserta = $id_peserta
    ) nilai_max 
  FROM tb_paket a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  WHERE b.id_room=$id_room 
  ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  if (isset($arr_data_act[$act][$d['id_sesi']])) {
    array_push($arr_data_act[$act][$d['id_sesi']], $d);
  } else {
    $arr_data_act[$act][$d['id_sesi']][0] = $d;
  }
}
