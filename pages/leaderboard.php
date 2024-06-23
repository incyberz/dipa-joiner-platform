<?php
set_h2('Leaderboard');
$stars = "<img src='$lokasi_img/icons/stars.png' height=25px>";
$get_update = $_GET['update'] ?? '';

$week = intval(strtotime('now') / (7 * 24 * 60 * 60));

# ============================================================
# MAIN SELECT
# ============================================================
$s = "SELECT 
a.deskripsi,
b.*,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best1 AND q.tahun_ajar=$tahun_ajar) kelas_best1,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best2 AND q.tahun_ajar=$tahun_ajar) kelas_best2,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best3 AND q.tahun_ajar=$tahun_ajar) kelas_best3,
(SELECT nama FROM tb_peserta WHERE id=b.best1) nama_best1,
(SELECT nama FROM tb_peserta WHERE id=b.best2) nama_best2,
(SELECT nama FROM tb_peserta WHERE id=b.best3) nama_best3

FROM tb_best a 
JOIN tb_best_week b ON a.best=b.best 
WHERE b.week=$week 
AND a.hidden IS NULL
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q) || $get_update) {
  include 'leaderboard-auto_update.php';
  exit;
} else {
  $best = '';
  while ($d = mysqli_fetch_assoc($q)) {
    // $id=$d['id'];
    $id_besties = [
      $d['best1'] => ['nama' => $d['nama_best1'], 'kelas' => $d['kelas_best1']],
      $d['best2'] => ['nama' => $d['nama_best2'], 'kelas' => $d['kelas_best2']],
      $d['best3'] => ['nama' => $d['nama_best3'], 'kelas' => $d['kelas_best3']]
    ];
    $div_peserta = '';
    $i = 0;
    foreach ($id_besties as $id_bestie => $arr_nama_kelas) {
      $i++;
      $src = "$lokasi_profil/wars/peserta-$id_bestie.jpg";
      if (!file_exists($src)) $src = "$lokasi_profil/peserta-$id_bestie.jpg";
      $div_peserta .= "
        <div style='position: relative'>
          <img src='$src' class=foto_profil>
          <div class='f12 darkblue'>$arr_nama_kelas[nama]</div>
          <div class='f12 abu miring'>$arr_nama_kelas[kelas]</div>
          <div style='position:absolute; top:80px; right:0'>
            <img src='$lokasi_img/gifs/juara-$i.gif' height=50px>
          </div>
        </div>
      ";
    }

    $AT = strtoupper(key2kolom($d['best']));

    $best .= "
      <div class='col-lg-6'>
        <div class='wadah tengah'>
          <h4 class='f16 '>
            $stars  
            <span class='upper green bold' style='display:inline-block; margin-top:15px'>
              THE BEST $AT
            </span> 
            $stars 
          </h4>
          <p>
            $d[deskripsi] 
          </p>
          <div class='flexy flex-center center border-bottom'>
            $div_peserta
          </div>
            <a href='?leaderboard&best=$d[best]'>view more</a>
        </div>
      </div>
    ";
  }
}

echo "<div class=row>$best</div>";
