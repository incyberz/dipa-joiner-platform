<?php
set_h2('Assign Kelas');
# ============================================================
# ASSIGN KELAS KE ROOM INI
# ============================================================
if (!$id_room) jsurl('?');
$get_untuk_kelas = $_GET['untuk_kelas'] ?? die('untuk_kelas undefined.');
$dari_id_room_kelas = $_GET['dari_id_room_kelas'] ?? '';

# ============================================================
# LIST LATIHAN
# ============================================================
// $s = "SELECT * FROM tb_latihan WHERE "

# ============================================================
# GET DATA LATIHAN DAN CHALLENGE DI ROOM INI PADA TA SKG
# ============================================================


if (!$dari_id_room_kelas) {
  $s = "SELECT a.*,
  CONCAT(b.prodi,'-',b.shift,'-',b.sub_kelas,'-SM',b.semester) kelas_show,
  (
    SELECT COUNT(1) FROM tb_paket_kelas WHERE kelas=b.kelas) kuis_count,
  (
    SELECT COUNT(1) FROM tb_assign_latihan WHERE id_room_kelas=a.id) latihan_count,
  (  
    SELECT COUNT(1) FROM tb_assign_challenge WHERE id_room_kelas=a.id) challenge_count
  
  FROM tb_room_kelas a 
  JOIN tb_kelas b ON a.kelas=b.kelas 
  WHERE a.id_room=$id_room 
  AND a.kelas != 'INSTRUKTUR' 
  AND a.ta=$ta_aktif
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $links = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $links .= "
      <li>
        <a href='?assign_kelas&dari_id_room_kelas=$id_room_kelas&kelas=$get_untuk_kelas'>$d[kelas]</a>
        <div class='f12 abu'>
          $d[kuis_count] kuis, 
          $d[latihan_count] tugas, 
          $d[challenge_count] challenges
        </div>
      </li>
    ";
  }
  echo "
  Copas Semua Tugas dan Challenges dari Kelas:
  <ul class=mt2>
    $links
  </ul>
  untuk kelas baru [$get_untuk_kelas].
  ";
}
