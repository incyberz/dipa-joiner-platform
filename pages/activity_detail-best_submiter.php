<?php
$best_submiter = '';
$stars = "<img src='$lokasi_img/icons/stars.png' height=25px>";
if ($count_submiter) {
  $s2 = "SELECT 
  (a.get_point + COALESCE(a.poin_antrian,0) + COALESCE(a.poin_apresiasi,0)) total_poin, 
  c.nama as nama_submiter, 
  c.image as image_submiter, 
  c.war_image as war_image_submiter,
  (
    SELECT p.kelas FROM tb_kelas_peserta p 
    JOIN tb_room_kelas q ON p.kelas=q.kelas 
    WHERE p.id_peserta=c.id 
    AND q.id_room=$id_room ) kelas_submiter 
  FROM tb_bukti_$jenis a 
  JOIN tb_assign_$jenis b ON a.id_assign_$jenis=b.id   
  JOIN tb_peserta c ON a.id_peserta=c.id
  WHERE b.id_$jenis=$id_jenis
  AND c.id_role=1 
  ORDER BY total_poin DESC, tanggal_upload  
  LIMIT 3 
  ";
  // die($s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $i = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $i++;
    $nama_submiter = $d2['nama_submiter'];
    // echo "<br>$nama_submiter";
    $best_submiter .= "
      <div style='position: relative'>
        <img src='$lokasi_profil/$d2[war_image_submiter]' class=foto_profil>
        <div class='f12 darkblue'>$d2[nama_submiter]</div>
        <div class='f12 abu miring'>$d2[kelas_submiter]</div>
        <div style='position:absolute; top:80px; right:0'>
          <img src='$lokasi_img/gifs/juara-$i.gif' height=50px>
        </div>
      </div>
    ";
  }
  $best_submiter = "
    <h4 class='f16'>$stars  <span class='upper green bold' style='display:inline-block; margin-top:15px'>Best $Submiter</span> $stars </h4>
    <div class='flexy flex-center center'>
      $best_submiter
    </div>
  ";
}
