<?php
if (!$id_room) die(div_alert('danger', 'harus login dahulu untuk mengakses fitur <span class=darkblue>' . basename(__FILE__, '.php') . '</span>'));
# ============================================================
# GET ROOM KELAS DAN INISIALISASI RANK
# ============================================================
$s = "SELECT kelas FROM tb_room_kelas WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  // $id=$d['id'];
  $arr_rank_kelas[$d['kelas']] = 0;
}


# ============================================================
# MAIN SELECT POIN
# ============================================================
$s = "SELECT 
a.id_peserta,
a.akumulasi_poin,
b.nama as nama_peserta,
b.image,
b.war_image,
d.kelas  

FROM tb_poin a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas_peserta c ON c.id_peserta=b.id 
JOIN tb_kelas d ON c.kelas=d.kelas  
JOIN tb_room_kelas e ON e.kelas=d.kelas  
WHERE a.id_room='$id_room' 
AND b.status = 1 -- _peserta aktif 
AND b.id_role = 1 -- _peserta only 
AND e.id_room = $id_room 
AND e.ta =  $ta 

ORDER BY a.akumulasi_poin DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$bestiers = '';
if (mysqli_num_rows($q)) {
  $room_rank = 0;
  $arr_best = [];
  while ($d = mysqli_fetch_assoc($q)) {
    $room_rank++;

    // for insert weekly best
    if ($room_rank <= 3) $arr_best[$room_rank] = $d['id_peserta'];

    // increment rank kelas
    $arr_rank_kelas[$d['kelas']]++;
    $rank_kelas = $arr_rank_kelas[$d['kelas']];

    # ============================================================
    # FINAL TR FOR PREVIEW PROCESS
    # ============================================================
    $tr .= "
      <tr>
        <td>$room_rank</td>
        <td>$d[id_peserta]</td>
        <td>$d[nama_peserta]</td>
        <td>$d[kelas]</td>
        <td>$rank_kelas</td>
        <td>$d[akumulasi_poin]</td>
      </tr>
    ";

    // add to bestiers $Room | kelas
    $image = $d['war_image'] ?? $d['image'];
    $bestiers .= "$d[id_peserta]|$d[nama_peserta]|$d[kelas]|$d[akumulasi_poin]|$image--";


    $s2 = "UPDATE tb_poin SET rank_room=$room_rank, rank_kelas=$rank_kelas WHERE id_peserta=$d[id_peserta] AND id_room=$id_room";
    echo "<br>$s2";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }
}

# ============================================================
# UPDATE BEST WEEK RANK_ROOM
# ============================================================
$best_code = 'rank_room';
$s_insert = "INSERT INTO tb_best_week (
  best_week, 
  best, 
  week, 
  id_room,
  best1, 
  best2, 
  best3, 
  bestiers
) VALUES (
  '$best_code-$week-$id_room', 
  '$best_code', 
  '$week', 
  '$id_room', 
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
echolog("<span class='blue f24 consolas'>Updating $best_code</span>");
echo '<hr>';
$q = mysqli_query($cn, $s_insert) or die(mysqli_error($cn));

# ============================================================
# FINAL UI
# ============================================================
$tb = $tr ? "
  <table class=table>
    <thead>
      <th>room_rank</th>
      <th>id_peserta</th>
      <th>nama_peserta</th>
      <th>kelas</th>
      <th>rank_kelas</th>
      <th>akumulasi_poin</th>
    </thead>
    $tr
  </table>
" : div_alert('danger', "Data poin tidak ditemukan.");
echo "$tb";
