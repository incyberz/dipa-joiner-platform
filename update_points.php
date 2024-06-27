<?php
// $harus_update_poin = 1; //zzz
// die("<h1>harus_update_poin: $harus_update_poin and id_room_kelas:$id_room_kelas</h1>");
if ($harus_update_poin and $id_room_kelas) {
  echo '<div class="consolas f12 abu">Updating Points... please wait!<hr>';
  # ========================================================
  # HITUNG MY RANK KELAS
  # ========================================================
  $s = "SELECT a.id_peserta 
  FROM tb_poin a 
  JOIN tb_peserta b ON a.id_peserta=b.id 
  JOIN tb_kelas_peserta c ON b.id=c.id_peserta 
  JOIN tb_kelas d ON c.kelas=d.kelas 
  WHERE a.id_room=$id_room 
  AND b.status = 1 
  AND b.id_role = 1 
  AND d.tahun_ajar = $tahun_ajar 
  AND d.status = 1 
  AND c.kelas = '$kelas'
  
  ORDER BY a.akumulasi_poin DESC;
  ";
  // echo $s;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $rank_kelas = 1;
  $i = 1;
  while ($d = mysqli_fetch_assoc($q)) {
    if ($d['id_peserta'] == $id_peserta) {
      $rank_kelas = $i;
      break;
    }
    $i++;
  }
  echo "<br>getting rank_kelas... rank: $rank_kelas";
  // die("<pre>$s</pre>");



  # ========================================================
  # HITUNG MY RANK GLOBAL
  # ========================================================
  $s = "SELECT a.id_peserta 
  FROM tb_poin a 
  JOIN tb_peserta b ON a.id_peserta=b.id 
  JOIN tb_kelas_peserta c ON b.id=c.id_peserta 
  JOIN tb_kelas d ON c.kelas=d.kelas 
  WHERE a.id_room=$id_room 
  AND b.status = 1 
  AND b.id_role = 1 
  AND d.tahun_ajar = $tahun_ajar 
  AND d.status = 1 
  
  ORDER BY a.akumulasi_poin DESC;
  ";
  // echo $s;
  // die("<pre>$s</pre>");
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $rank_room = 1;
  $i = 1;
  while ($d = mysqli_fetch_assoc($q)) {
    if ($d['id_peserta'] == $id_peserta) {
      $rank_room = $i;
      break;
    }
    $i++;
  }
  echo "<br>updating rank_room... rank: $rank_room";

  # ========================================================
  # HITUNG MY POIN PRESENSI
  # HITUNG MY POIN BERTANYA
  # HITUNG MY POIN MENJAWAB
  # HITUNG MY POIN LATIHAN
  # HITUNG MY POIN CHALLENGE 
  # HITUNG MY POIN PLAY KUIS
  # HITUNG MY POIN TANAM SOAL 
  # HITUNG MY AKUMULASI POIN
  # ========================================================
  $s = "SELECT 
    (
      SELECT poin_presensi 
      FROM tb_presensi_summary   
      WHERE id_peserta=a.id 
      AND id_room=$id_room ) poin_presensi,
    (
      SELECT SUM(poin) 
      FROM tb_bertanya  
      WHERE id_penanya=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null) poin_bertanya,
    (
      SELECT SUM(poin) 
      FROM tb_bertanya_reply  
      WHERE id_penjawab=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null) poin_menjawab,
    (
      SELECT SUM(p.get_point) 
      FROM tb_bukti_latihan p 
      JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND status=1
      AND p.tanggal_verifikasi is not null) poin_latihan, 
    (
      SELECT SUM(p.get_point) 
      FROM tb_bukti_challenge p 
      JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND status=1 
      AND p.tanggal_verifikasi is not null ) poin_challenge, 
    (
      SELECT (war_point_quiz + war_point_reject) FROM tb_war_summary   
      WHERE id_peserta=a.id 
      AND id_room = $id_room) poin_play_kuis,
    (
      SELECT war_point_passive FROM tb_war_summary   
      WHERE id_peserta=a.id 
      AND id_room = $id_room) poin_tanam_soal 

  FROM tb_peserta a 
  WHERE a.id=$id_peserta 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $akumulasi_poin = 0;
  foreach ($d as $key => $poin) {
    $akumulasi_poin += $poin;
    echo "<br>updating $key... poin: $poin";
  }


  # ========================================================
  # RE-UPDATE MY POINTS
  # ========================================================
  $s = "UPDATE tb_poin SET 

  rank_kelas = $rank_kelas,
  rank_room = $rank_room,
  poin_presensi = '$d[poin_presensi]',
  poin_bertanya = '$d[poin_bertanya]',
  poin_menjawab = '$d[poin_menjawab]',
  poin_latihan = '$d[poin_latihan]',
  poin_challenge = '$d[poin_challenge]',
  poin_play_kuis = '$d[poin_play_kuis]',
  poin_tanam_soal = '$d[poin_tanam_soal]',
  akumulasi_poin = $akumulasi_poin,

  last_update_point = CURRENT_TIMESTAMP 

  WHERE id_room=$id_room
  AND id_peserta=$id_peserta
  ";
  echo "<br>updating poin data... ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo 'success.';

  jsurl();
}
