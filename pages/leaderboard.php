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
$stars = "<img src='$lokasi_img/icon/stars.png' height=25px>";
$get_update = $_GET['update'] ?? '';
$get_best = $_GET['best'] ?? '';
$sql_best = $get_best ? "a.best = '$get_best'" : 1;

$bulan_tahun = $nama_bulan[intval(date('m')) - 1] . ' ' . date('Y');
$Leaderboard = $id_room ? 'Room Leaderboard' : 'Leaderboard';
$Leaderboard = $parameter ? $Leaderboard : $meta_title;
$nama_room_show = $id_room ? " - <span class=darkblue>$nama_room</span> " : '';
set_h2($Leaderboard, "Peserta Terbaik minggu ini - $bulan_tahun $nama_room_show - <i>all time</i>");
include 'leaderboard-functions.php';


# ============================================================
# MAIN SELECT ALL BEST
# ============================================================
$sql_id_room = $id_room ? "b.id_room = $id_room" : "b.id_room is null";
$sql_kelas = $kelas ? "b.kelas = '$kelas'" : 1;
$sql_kelas = 1;
$s = "SELECT 
a.best,
a.deskripsi,
a.satuan,
b.*,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best1 AND q.tahun_ajar=$ta) kelas_best1,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best2 AND q.tahun_ajar=$ta) kelas_best2,
(
  SELECT p.kelas FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas
  WHERE p.id_peserta=b.best3 AND q.tahun_ajar=$ta) kelas_best3,
(SELECT nama FROM tb_peserta WHERE id=b.best1) nama_best1,
(SELECT nama FROM tb_peserta WHERE id=b.best2) nama_best2,
(SELECT nama FROM tb_peserta WHERE id=b.best3) nama_best3

FROM tb_best a 
JOIN tb_best_week b ON a.best=b.best 
WHERE b.week=$week 
AND a.hidden IS NULL 
AND $sql_best
AND $sql_id_room
AND $sql_kelas
ORDER BY a.no
";
// echo '<pre>';
// var_dump($s);
// echo '</pre>';
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if ($get_update) {
  echolog('including leaderboard-auto_update.php first');
  include 'leaderboard-auto_update.php';
  exit;
} else {
  $tr_best = '';
  if ($get_best) { // single best
    # ============================================================
    # SINGLE BEST
    # ============================================================
    $d = mysqli_fetch_assoc($q);
    $satuan = $d['satuan'] ? $d['satuan'] : 'LP';
    $AT = strtoupper(key2kolom($d['best']));
    $arr_bestiers = explode('--', $d['bestiers']);
    $i = 0;
    foreach ($arr_bestiers as $key => $v) {
      if (!$v) continue;
      $i++;
      if (($id_role == 1 || !$id_role) and $i > 10) break;
      $arr2 = explode('|', $v);
      $id_bestie = $arr2[0];
      $nama = $arr2[1];
      $kelas = $arr2[2];
      $poin = $arr2[3] ?? 0;
      // $image = $arr2[4] ?? die(div_alert('danger', "Peserta wajib ada image profil"));
      $image = $arr2[4] ?? 'profil_na.jpg';

      $poin_show = $poin ? number_format($poin) : 0;

      $aos_delay = $i * 60;
      $src = "$lokasi_profil/wars/$image";
      if (!file_exists($src)) $src = "$lokasi_profil/$image";
      if (!file_exists($src)) $src = $src_profil_na_fixed;
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
      } elseif (!$poin) {
        $gradasi = 'merah';
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
    # SINGLE BEST UI
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
  } else { // multiple 3 best






























    $div_best = '';
    # ============================================================
    # BEST ROOM PLAYER IF LOGIN
    # ============================================================
    if ($id_room and $kelas and $id_room_kelas) {

      # ============================================================
      # GET ROOM KELAS DAN INISIALISASI RANK
      # ============================================================
      $s2 = "SELECT kelas FROM tb_room_kelas WHERE id_room=$id_room -- AND kelas!='INSTRUKTUR'";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      while ($d = mysqli_fetch_assoc($q2)) {
        $arr_rank_kelas[$d['kelas']] = [];
      }

      # ============================================================
      # GET DATA POIN
      # ============================================================
      $s2 = "SELECT 
      a.id as id_poin,
      a.akumulasi_poin,
      a.rank_room,
      a.rank_kelas,
      b.id as id_peserta,
      b.nama as nama_peserta,
      d.kelas 

      FROM tb_poin a 
      JOIN tb_peserta b ON a.id_peserta=b.id 
      JOIN tb_kelas_peserta c ON b.id=c.id_peserta 
      JOIN tb_kelas d ON c.kelas=d.kelas 
      JOIN tb_room_kelas e ON e.kelas=d.kelas 
      WHERE a.id_room=$id_room 
      AND a.rank_kelas <= 10 
      AND e.id_room = $id_room  
      AND b.status = 1 -- peserta aktif
      AND b.id_role = 1 -- peserta only 
      ORDER BY a.akumulasi_poin DESC
      ";

      // echo '<pre>';
      // var_dump($s2);
      // echo '</pre>';
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $arr_rank_room = [];
      $i = 0;
      while ($d = mysqli_fetch_assoc($q2)) {
        $i++;
        if ($i <= 10) {
          $arr_rank_room[$d['rank_room']] = [
            'id' => $d['id_peserta'],
            'nama' => $d['nama_peserta'],
            'kelas' => $d['kelas'],
            'poin' => $d['akumulasi_poin'],
          ];
        }
        $arr_rank_kelas[$d['kelas']][$d['rank_kelas']] = [
          'id' => $d['id_peserta'],
          'nama' => $d['nama_peserta'],
          'kelas' => $d['kelas'],
          'poin' => $d['akumulasi_poin'],
        ];
      }

      $arr_room_kelas = [
        'rank_room' => [
          'title' => 'THE BEST ROOM PLAYER',
          'desc' => "Player Terbaik di Room <b class=darkblue>$nama_room</b>",
          'data' => $arr_rank_room
        ],
        'rank_kelas' => [
          'title' => 'THE BEST PLAYER in ' . $kelas,
          'desc' => "Player Terbaik di Kelas <b class=darkblue>$kelas</b> | $nama_room",
          'data' => $arr_rank_kelas[$kelas]
        ]
      ];

      foreach ($arr_room_kelas as $best_code => $v) {
        $div_peserta = '';
        if ($best_code == 'rank_kelas' and $kelas == 'INSTRUKTUR') {
          $div_peserta .= div_alert('info', 'Leaderboard INSTRUKTUR terdapat di <a href="?">Dashboard</a>');
        } else {
          $i = 0;
          foreach ($v['data'] as $best_no => $arr_bestie) {
            if ($i == 3) break;
            $i++;
            $id_bestie = $arr_bestie['id'];
            $class_style = $id_bestie == $id_peserta ? 'br10 gradasi-pink border_mine' : '';
            $src = "$lokasi_profil/wars/peserta-$id_bestie.jpg";
            if (!file_exists($src)) $src = "$lokasi_profil/peserta-$id_bestie.jpg";
            if (!file_exists($src)) $src = $src_profil_na_fixed;
            $div_peserta .= "
              <div style='position: relative;' class='$class_style '>
                <img src='$src' class=foto_profil>
                <div class='f12 darkblue'>$arr_bestie[nama]</div>
                <div class='f12 abu miring'>$arr_bestie[kelas]</div>
                <div style='position:absolute; top:80px; right:0'>
                  <img src='$lokasi_img/gif/juara-$i.gif' height=50px>
                </div>
              </div>
            ";
          }
        }

        $div_best .= div_best(
          'rank_room',
          $div_peserta,
          $stars,
          $v['title'],
          $v['desc'],
          'gradasi-toska'
        );
      }
    }



    # ============================================================
    # ALL BEST PUBLIC | NON RANK_ROOM OR RANK_KELAS
    # ============================================================
    $count_blok_public = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      if ($d['best'] == 'rank_room' || $d['best'] == 'rank_kelas') continue;
      $count_blok_public++;
      $AT = strtoupper(key2kolom($d['best']));
      $id_besties = [
        $d['best1'] => ['nama' => $d['nama_best1'], 'kelas' => $d['kelas_best1']],
        $d['best2'] => ['nama' => $d['nama_best2'], 'kelas' => $d['kelas_best2']],
        $d['best3'] => ['nama' => $d['nama_best3'], 'kelas' => $d['kelas_best3']]
      ];
      $div_peserta = '';
      $i = 0;
      // echo '<pre>';
      // var_dump($id_besties);
      // echo '</pre>';
      foreach ($id_besties as $id_bestie => $arr_nama_kelas) {
        if (!$arr_nama_kelas['nama']) continue;
        $i++;
        $class_style = $id_bestie == $id_peserta ? 'br10 gradasi-pink border_mine' : '';
        $src = "$lokasi_profil/wars/peserta-$id_bestie.jpg";
        if (!file_exists($src)) $src = "$lokasi_profil/peserta-$id_bestie.jpg";
        if (!file_exists($src)) $src = $src_profil_na_fixed;

        $div_peserta .= "
          <div style='position: relative;' class='$class_style '>
            <img src='$src' class=foto_profil>
            <div class='f12 darkblue'>$arr_nama_kelas[nama]</div>
            <div class='f12 abu miring'>$arr_nama_kelas[kelas]</div>
            <div style='position:absolute; top:80px; right:0'>
              <img src='$lokasi_img/gif/juara-$i.gif' height=50px>
            </div>
          </div>
        ";
      }

      $div_best .= div_best(
        $d['best'],
        $div_peserta,
        $stars,
        "THE BEST $AT",
        $d['deskripsi']
      );
    }
    if (!$count_blok_public) {
      echolog('count of count_blok_public is null... perform PUBLIC AUTO_UPDATE');
      echolog('including leaderboard-auto_update.php after best RANK_ROOM');
      include 'leaderboard-auto_update.php';
    }
  }
}

echo "<div class=row>$div_best</div>";
