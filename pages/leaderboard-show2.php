<?php
# ============================================================
# LEADERBOARD KELAS ATAU GLOBAL
# ============================================================
// login_only();
$Best_Class = $global ? 'Global Rank' : 'Best Class';
$link = $global ? '<a href=?leaderboard>Best Class</a>' : '<a href=?leaderboard&global=1>Global Rank</a>';
$judul = $global ? 'Global Rank' : 'The Best Top 10';

$link_public = ''; // skipped
set_h2($judul, "$Best_Class minggu ke-$week -  $bulan | $link $link_public");


# =========================================================
# INITIAL VARIABLE FROM TB_POIN
# =========================================================
$sql_kelas = '';
if (!$global) {
  $target_kelas = $id_role == 1 ? $kelas : $target_kelas; // target kelas adalah kelas sendiri bagi mhs
  $sql_kelas = $target_kelas ? "AND c.kelas = '$target_kelas'" : '';
}

$s = "SELECT 
a.id,
a.war_image,
a.id as id_peserta,
UPPER(a.nama) as nama_peserta,
a.image,
c.kelas,
a.nama,
f.akumulasi_poin 

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
JOIN tb_room e ON d.id_room=e.id 
JOIN tb_poin f ON a.id=f.id_peserta
WHERE d.id_room = $id_room 
AND a.id_role = 1 -- mhs only
AND a.status = 1 -- mhs aktif 
AND 1 -- d.ta = $ta_aktif 
AND c.ta = $ta_aktif -- Kelas Aktif
AND e.status = 100 -- Room Aktif 
AND f.id_room = $id_room -- data poin room ini  
$sql_kelas
ORDER BY f.akumulasi_poin DESC
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

// $tr = '';
$i = 0;
$rids = [];
$btop = '';
$juara4 = '';
$my_rank = '';
while ($d = mysqli_fetch_assoc($q)) {
  $rids[$i] = $d;
  $i++;
  $rank = $i + 1;
  if ($d['id_peserta'] == $id_peserta) $my_rank = $i;
  if ($i < 3) continue;
  if ($i > 3) $btop = 'btop';
  $poin = number_format($d['akumulasi_poin'], 0);
  $nama = $d['nama_peserta'];
  $kelas = $global ? "<div class='f10 abu'>$d[kelas]</div>" : '';
  $my_row = $d['id_peserta'] == $id_peserta ? 'border-top: solid 5px #ccf;border-bottom: solid 5px #ccf; background: linear-gradient(#efe,#ffc); margin: 0 -12px 0 -12px;padding:12px' : '';
  if ($i > 10 and $id_role == 1 and $d['id_peserta'] != $id_peserta) continue; // limit data untuk mhs

  $juara4 .=  "
    <div class='flexy $btop ' style='$my_row'>
      <div class='mt1 mb1' style='flex: 1'>$rank <span class='rank_th f10'>th</span></div>
      <div class='mt1 mb1' style='flex: 3'>$nama$kelas</div>
      <div class='mt1 mb1' style='flex: 2'>$poin <span class='kecil miring abu'>LP</span></div>
    </div>
  ";

  // $tr .= "
  //   <tr id=tr__$d[id]>
  //     <td>$i</td>
  //     <td>$d[nama]</td>
  //     <td>$d[kelas]</td>
  //     <td>$d[akumulasi_poin]</td>
  //     <td>Edit | Delete</td>
  //   </tr>
  // ";
}

// echo "
//   <table class=table>
//     $tr
//   </table>";
// $count = mysqli_num_rows($q);
// echo '<pre>';
// var_dump($count);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;


// if ($global) {
//   $rids = $rrank_room;
// } else {
//   $target_kelas = $id_role == 1 ? $kelas : $target_kelas; // target kelas adalah kelas sendiri bagi mhs
//   $rids = $rrank_kelas[$target_kelas] ?? die("Tidak ada data rank untuk kelas: $target_kelas");
// }

// echo '<pre>';
// var_dump($rrank_kelas[$target_kelas]);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;


# =========================================================
# MAIN SELECT
# =========================================================
/*
$rids = [];
$my_rank = null; // posisi rank saya 
foreach ($rids as $k => $id_pes) {
  $rank = $k + 1;
  if ($id_pes == $id_peserta) $my_rank = $rank; // posisi rank saya 
  if ($k > 9 and $id_role == 1 and $id_pes != $id_peserta) continue; // limit data untuk mhs
  $rpoin = $row[$id_pes];
  $poin = 0;
  foreach ($rpoin as $k2 => $v2) {
    if ($k2 == 'rank_kelas' || $k2 == 'rank_room') continue;
    $poin += intval($v2);
  }

  $img = '';
  if ($id_role == 2 || $id_pes == $id_peserta) {

    $src1 = !$d['war_image'] ? $src_profil_na : "$lokasi_profil/$d[war_image]";
    $src2 = !$d['image'] ? $src_profil_na : "$lokasi_profil/$d[image]";
    $src = $src_profil_na;
    if (file_exists($src1)) {
      $src = $src1;
    } elseif (file_exists($src2)) {
      $src = $src2;
    }


    $img = "<img src='$src' class='profil_pembuat' ><br> ";
  }

  array_push($rids, [
    'id' => $d['id_peserta'],
    'nama' => $img . $d['nama_peserta'],
    'rank' => $rank,
    'poin' => $poin,
    'kelas' => $d['kelas'],
  ]);
}
  */

// echo '<pre>';
// var_dump($my_rank);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;

$juara1 = ucwords(strtolower($rids[0]['nama']));
$juara2 = ucwords(strtolower($rids[1]['nama']));
$juara3 = ucwords(strtolower($rids[2]['nama']));

$poin_juara1 = number_format($rids[0]['akumulasi_poin']);
$poin_juara2 = number_format($rids[1]['akumulasi_poin']);
$poin_juara3 = number_format($rids[2]['akumulasi_poin']);

$kelas_juara1 = !$global ? '' : '<div class="f10">' . $rids[0]['kelas'] . '</div>';
$kelas_juara2 = !$global ? '' : '<div class="f10">' . $rids[1]['kelas'] . '</div>';
$kelas_juara3 = !$global ? '' : '<div class="f10">' . $rids[2]['kelas'] . '</div>';

$border1 = $my_rank == 1 ? 'border: solid 4px blue' : '';
$border2 = $my_rank == 2 ? 'border: solid 4px blue' : '';
$border3 = $my_rank == 3 ? 'border: solid 4px blue' : '';


echo "
  <div class='wadah gradasi-hijau mx-auto' style='max-width:500px'>

    <div class='wadah tengah ' style='background: linear-gradient(#ffbbff,#fef);$border1'>
      <img src=assets/img/gif/medal1-1.gif height=90px>
      <div class='darkblue mt1 f20'>$juara1$kelas_juara1</div>
      <div class=' darkblue '>$poin_juara1 LP</div>
    </div>


    <div class=row>
      <div class=col-6>
        <div class='wadah tengah bg-white' style='$border2'>
          <img src=assets/img/gif/medal2-1.gif height=70px>
          <div class='darkblue mt1'>$juara2$kelas_juara2</div>
          <div class='kecil darkblue'>$poin_juara2 LP</div>
        </div>
      </div>
      <div class=col-6>
        <div class='wadah tengah bg-white' style='$border3'>
          <img src=assets/img/gif/medal3-1.gif height=70px>
          <div class='darkblue mt1'>$juara3$kelas_juara3</div>
          <div class='kecil darkblue'>$poin_juara3 LP</div>
        </div>
      </div>
    </div>

    <div class='wadah bg-white' style='background: linear-gradient(#fff,#aff)'>
      $juara4
    </div>


    
  </div>
";

















?>
<script>
  $(function() {

  })
</script>