<?php
$arr_act = ['latihan', 'challenge', 'paket']; // paket soal for quiz
$arr_data_act = [];
foreach ($arr_act as $act) {
  $s = "SELECT a.id, a.id_sesi,
  b.nama as nama_act,
  b.ket 
  FROM tb_assign_$act a
  JOIN tb_$act b ON a.id_$act=b.id
  WHERE id_room_kelas='$id_room_kelas' 
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

$arr_latihan = $arr_data_act['latihan'] ?? [];
$arr_challenge = $arr_data_act['challenge'] ?? [];
