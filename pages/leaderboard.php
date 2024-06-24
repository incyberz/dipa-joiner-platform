<style>
  .foto_profil_small {
    height: 80px;
    width: 80px;
  }

  .border_mine {
    border: solid 3px blue
  }

  @media (max-width: 400px) {
    .hide-at-mobile {
      display: none;
    }
  }
</style>
<?php
$stars = "<img src='$lokasi_img/icons/stars.png' height=25px>";
$get_update = $_GET['update'] ?? '';
$get_best = $_GET['best'] ?? '';
$sql_best = $get_best ? "a.best = '$get_best'" : 1;

$bulan_tahun = $nama_bulan[intval(date('m')) - 1] . ' ' . date('Y');
set_h2('Leaderboard', "Peserta Terbaik minggu ke-$week - $bulan_tahun - <i>all time</i>");


# ============================================================
# MAIN SELECT ALL BEST
# ============================================================
$s = "SELECT 
a.deskripsi,
a.satuan,
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
AND $sql_best
ORDER BY a.no
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q) || $get_update) {
  include 'leaderboard-auto_update.php';
  exit;
} else {
  $tr_best = '';
  if ($get_best) {
    # ============================================================
    # SINGLE BEST
    # ============================================================
    $d = mysqli_fetch_assoc($q);
    $satuan = $d['satuan'] ? $d['satuan'] : 'LP';
    $AT = strtoupper(key2kolom($d['best']));
    $arr = explode('||', $d['bestiers']);
    $i = 0;
    foreach ($arr as $key => $v) {
      if (!$v) continue;
      $i++;
      $arr2 = explode('|', $v);
      $id_bestie = $arr2[0];
      $nama = $arr2[1];
      $kelas = $arr2[2];
      $poin = $arr2[3];
      $image = $arr2[4] ?? die(div_alert('danger', "Peserta wajib ada image profil"));

      $poin_show = number_format($poin);

      $aos_delay = $i * 60;
      $src = "$lokasi_profil/wars/$image";
      if (!file_exists($src)) $src = "$lokasi_profil/$image";
      $the_stars = '&nbsp;';
      $gradasi = '';
      if ($i == 1) {
        $the_stars = "$stars$stars$stars";
        $gradasi = 'pink';
      } elseif ($i == 2) {
        $the_stars = "$stars$stars";
        $gradasi = 'biru';
      } elseif ($i == 3) {
        $the_stars = "$stars";
        $gradasi = 'toska';
      }

      $border_mine = $id_bestie == $id_peserta ? 'border_mine' : '';

      $tr_best .= "
        <tr class='gradasi-$gradasi $border_mine'>
          <td class='kanan hide-at-mobile'>
            $the_stars
          </td>
          <td class=tengah>
            <div class='abu miring f12 mb2 mt2'>RANK</div>
            <div class='darkblue f34'>$i</div>
          </td>
          <td>
            <img src='$src' class='foto_profil foto_profil_small'>
          </td>
          <td>
            <div class='darkblue mt4'>$nama</div>
            <div class='abu miring f12'>$kelas</div>
            <div class='f14 '>$poin_show $satuan</div>
          </td>
        </tr>
      ";
    }

    # ============================================================
    # SINGLE BEST
    # ============================================================
    $div_best = "
      <div class='tengah' data-aos='fade-up'>
        <a href='?leaderboard'>$img_prev</a>
        <h4 class='f16 tengah'>
          $stars  
          <span class='upper green bold' style='display:inline-block; margin-top:15px'>
            THE BEST $AT
          </span> 
          $stars 
        </h4>
        <p class=abu>
          $d[deskripsi] 
        </p>
        <div class='flex flex-center'>
          <div style='max-width:600px'>
            <table class='table table-hover'>$tr_best</table>
          </div>
        </div>
      </div>
    ";
  } else {






























    # ============================================================
    # ALL BEST
    # ============================================================
    $div_best = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $AT = strtoupper(key2kolom($d['best']));
      $id_besties = [
        $d['best1'] => ['nama' => $d['nama_best1'], 'kelas' => $d['kelas_best1']],
        $d['best2'] => ['nama' => $d['nama_best2'], 'kelas' => $d['kelas_best2']],
        $d['best3'] => ['nama' => $d['nama_best3'], 'kelas' => $d['kelas_best3']]
      ];
      $div_peserta = '';
      $i = 0;
      foreach ($id_besties as $id_bestie => $arr_nama_kelas) {
        $i++;
        $class_style = $id_bestie == $id_peserta ? 'br10 gradasi-pink border_mine' : '';
        $src = "$lokasi_profil/wars/peserta-$id_bestie.jpg";
        if (!file_exists($src)) $src = "$lokasi_profil/peserta-$id_bestie.jpg";
        $div_peserta .= "
          <div style='position: relative;' class='$class_style '>
            <img src='$src' class=foto_profil>
            <div class='f12 darkblue'>$arr_nama_kelas[nama]</div>
            <div class='f12 abu miring'>$arr_nama_kelas[kelas]</div>
            <div style='position:absolute; top:80px; right:0'>
              <img src='$lokasi_img/gifs/juara-$i.gif' height=50px>
            </div>
          </div>
        ";
      }

      $div_best .= "
        <div class='col-lg-6'  data-aos='fade-up'>
          <div class='wadah tengah'>
            <h4 class='f16'>
              $stars  
              <span class='upper green bold' style='display:inline-block; margin-top:15px'>
                THE BEST $AT
              </span> 
              $stars 
            </h4>
            <p class=abu>
              $d[deskripsi] 
            </p>
            <div class='flexy flex-center center border-bottom pb2'>
              $div_peserta
            </div>
              <a href='?leaderboard&best=$d[best]'>view more</a>
          </div>
        </div>
      ";
    }
  }
}

echo "<div class=row>$div_best</div>";
