<?php
$row = [];
$d = mysqli_fetch_assoc($q);
$t = explode('|', $d['poin_kbms']);
foreach ($t as $k => $v) {
  if ($v) {
    $t2 = explode(':', $v);
    $id_pes = $t2[0];

    $t3 = explode(',', $t2[1]);

    $row[$id_pes] = [
      'presensi' => $t3[0],
      'latihan' => $t3[1],
      'challenge' => $t3[2],
      'play_kuis' => $t3[3],
      'tanam_soal' => $t3[4],
    ];
  }
}

$t = explode('|', $d['rank_kelass']);
$rrank_kelas = [];
foreach ($t as $k => $v) {
  if ($v) {
    $t2 = explode(':', $v);
    $ckelas = $t2[0];
    $rrank_kelas[$ckelas] = [];

    $t3 = explode(',', $t2[1]);

    foreach ($t3 as $key_rank => $id_pes) {
      if ($id_pes) {
        $row[$id_pes]['rank_kelas'] = $key_rank + 1;
        array_push($rrank_kelas[$ckelas], $id_pes);
      }
    }
  }
}

$t = explode('|', $d['rank_rooms']);
$rrank_room = [];
foreach ($t as $k => $v) {
  if ($v) {
    $t2 = explode(',', $v);
    foreach ($t2 as $key_rank => $id_pes) {
      if ($id_pes) {
        $row[$id_pes]['rank_room'] = $key_rank + 1;
        array_push($rrank_room, $id_pes);
      }
    }
  }
}
