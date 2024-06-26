<?php
echolog('<span class="blue f24 consolas">Auto Update Leaderboards</span>... Please wait');
echo '<hr>';

$arr_s = [];

# ============================================================
# SELECT CHALLENGER 
# ============================================================
$arr_s['challenger'] = "SELECT 
  SUM(p.get_point + COALESCE(p.poin_antrian,0) + COALESCE(p.poin_apresiasi,0)) 
  FROM tb_bukti_challenge p 
  WHERE id_peserta=a.id
  AND p.status = 1 -- Verified Poin
";

# ============================================================
# SELECT SUBMITER 
# ============================================================
$arr_s['submiter'] = "SELECT
  SUM(p.get_point + COALESCE(p.poin_antrian,0) + COALESCE(p.poin_apresiasi,0)) 
  FROM tb_bukti_latihan p 
  WHERE id_peserta=a.id
  AND p.status = 1 -- Verified Poin
";



# ============================================================
# SELECT INVESTOR
# ============================================================
$arr_s['investor'] = "SELECT 
COUNT(1) 
FROM tb_soal_peserta p
WHERE p.id_pembuat=a.id 
AND (p.id_status is null or p.id_status > 0)
";

# ============================================================
# SELECT PLAY QUIZ
# ============================================================
$arr_s['play_quiz'] = "SELECT 
  SUM(p.poin_penjawab) 
  FROM tb_war p 
  JOIN tb_room q ON p.id_room=q.id 
  WHERE p.id_penjawab=a.id
  AND q.status = 100 -- Active Room 
  AND q.tahun_ajar=$tahun_ajar 
";

# ============================================================
# ONTIMER PRESENSI
# ============================================================
$arr_s['ontimer'] = "SELECT 
  SUM(p.poin) 
  FROM tb_presensi p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  JOIN tb_room r ON q.id_room=r.id 
  WHERE p.id_peserta=a.id 
  AND p.is_ontime = 1 
  AND r.status = 100 -- Active Room 
  AND r.tahun_ajar=$tahun_ajar 
";



# ============================================================
# ACCURACY
# ============================================================
$arr_s['accuracy'] = "SELECT 
  (
    (SELECT COUNT(1) FROM tb_war WHERE is_benar=1 AND id_room=q.id AND id_penjawab=p.id_penjawab)*100
    /
    (SELECT COUNT(1) FROM tb_war WHERE id_room=q.id AND id_penjawab=p.id_penjawab)
    *
    (SELECT COUNT(1) FROM tb_war WHERE id_room=q.id AND id_penjawab=p.id_penjawab)
  )
  FROM tb_war p 
  JOIN tb_room q ON p.id_room=q.id 
  WHERE p.id_penjawab=a.id
  AND q.status = 100 -- Active Room 
  AND q.tahun_ajar=$tahun_ajar 
  LIMIT 1
";































$s_best = "SELECT * FROM tb_best WHERE hidden IS NULL ORDER BY no ";
$q_best = mysqli_query($cn, $s_best) or die(mysqli_error($cn));
while ($d_best = mysqli_fetch_assoc($q_best)) {
  $best_code = $d_best['best'];
  if ($best_code == 'room_player' || $best_code == 'room_kelas') {
    include 'leaderboard-auto_update-update_room_player_rank.php';
    exit;
  }
  if (!$arr_s[$best_code]) die(div_alert('danger', "Belum ada String SQL untuk update <b class=darkblue>$best_code</b>"));
  $s2 = "SELECT 
    a.id as id_peserta,
    a.nama as nama_peserta,
    a.image,
    a.war_image,
    c.kelas,
    ($arr_s[$best_code]) best_value

    FROM tb_peserta a 
    JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
    JOIN tb_kelas c ON b.kelas=c.kelas 
    WHERE a.status = 1 -- Peserta Aktif 
    AND a.id_role = 1 -- Peserta Only 
    AND a.image IS NOT NULL -- Peserta punya Image
    AND c.status = 1 -- Kelas Aktif 
    AND c.kelas_non_peserta IS NULL -- Kelas Peserta Only 
    AND c.tahun_ajar = $tahun_ajar 

    ORDER BY best_value DESC 
    LIMIT 10  
  ";
  $q = mysqli_query($cn, $s2) or die(mysqli_error($cn));

  // if ($best_code == 'play_quiz') {
  //   echo '<pre>';
  //   var_dump($s2);
  //   echo '</pre>';
  //   exit;
  // }

  $bestiers = '';
  $arr_best = [];
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $arr_best[$i] = $d['id_peserta'];
    $image = $d['war_image'] ?? $d['image'];
    $bestiers .= "$d[id_peserta]|$d[nama_peserta]|$d[kelas]|$d[best_value]|$image||";
    if ($i <= 3) echo "<br>$d[nama_peserta] | $d[kelas] | $d[best_value]";
  }

  $s_insert = "INSERT INTO tb_best_week (
    best_week, 
    best, 
    week, 
    best1, 
    best2, 
    best3, 
    bestiers
  ) VALUES (
    '$best_code-$week', 
    '$best_code', 
    '$week', 
    '$arr_best[1]', 
    '$arr_best[2]', 
    '$arr_best[3]', 
    '$bestiers'    

  ) ON DUPLICATE KEY UPDATE 
    best = '$best_code', 
    week = $week,
    best1 = '$arr_best[1]',  
    best2 = '$arr_best[2]',  
    best3 = '$arr_best[3]',  
    bestiers = '$bestiers'
  ";

  echo '<br>';
  echolog("<span class='blue f24 consolas'>Updating $d_best[best]</span>");
  echo '<hr>';
  $q = mysqli_query($cn, $s_insert) or die(mysqli_error($cn));
}

jsurl('?leaderboard', 3000);
