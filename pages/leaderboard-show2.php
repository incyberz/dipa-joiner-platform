<?php
include 'leaderboard-reset.php';
include 'leaderboard-styles.php';
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
  if ($i < 4) continue;
  if ($i > 4) $btop = 'btop';
  $poin = number_format($d['akumulasi_poin'], 0);
  $nama = $d['nama_peserta'];
  $kelas = $global ? "<div class='f10 abu'>$d[kelas]</div>" : '';
  $my_row = $d['id_peserta'] == $id_peserta ? 'border-top: solid 5px #ccf;border-bottom: solid 5px #ccf; background: linear-gradient(#efe,#ffc); margin: 0 -12px 0 -12px;padding:12px' : '';
  if ($i > 10 and $id_role == 1 and $d['id_peserta'] != $id_peserta) continue; // limit data untuk mhs

  $img = $id_role == 2 ? cek_src_profil($d['image'], $d['war_image'], $lokasi_profil) : '';

  $juara4 .=  "
    <div class='flexy $btop ' style='$my_row'>
      <div class='mt1 mb1' style='flex: 1'>$rank <span class='rank_th f10'>th</span></div>
      <div class='mt1 mb1' style='flex: 3'>$nama$kelas</div>
      <div class='mt1 mb1' style='flex: 2'>$poin <span class='kecil miring abu'>LP</span></div>
    </div>
  ";
}

$rids_belum_ada = [
  'id' => 0,
  'nama_peserta' => 'Belum Ada',
  'rank' => 0,
  'akumulasi_poin' => 0,
  'kelas' => '',
  'image' => null,
  'war_image' => null,
];



$rids[0] = $rids[0] ?? $rids_belum_ada;
$rids[1] = $rids[1] ?? $rids_belum_ada;
$rids[2] = $rids[2] ?? $rids_belum_ada;

$img1 = $id_role == 2 ? cek_src_profil($rids[0]['image'], $rids[0]['war_image'], $lokasi_profil) : '';
$img2 = $id_role == 2 ? cek_src_profil($rids[1]['image'], $rids[1]['war_image'], $lokasi_profil) : '';
$img3 = $id_role == 2 ? cek_src_profil($rids[2]['image'], $rids[2]['war_image'], $lokasi_profil) : '';

$juara1 = ucwords(strtolower($rids[0]['nama_peserta']));
$juara2 = ucwords(strtolower($rids[1]['nama_peserta']));
$juara3 = ucwords(strtolower($rids[2]['nama_peserta']));

$juara1 = $id_role == 2 ? "<img src='$img1' class=profil_pembuat /><br>$juara1" : $juara1;
$juara2 = $id_role == 2 ? "<img src='$img2' class=profil_pembuat /><br>$juara2" : $juara2;
$juara3 = $id_role == 2 ? "<img src='$img3' class=profil_pembuat /><br>$juara3" : $juara3;

$poin_juara1 = number_format($rids[0]['akumulasi_poin']);
$poin_juara2 = number_format($rids[1]['akumulasi_poin']);
$poin_juara3 = number_format($rids[2]['akumulasi_poin']);

$kelas_juara1 = !$global ? '' : '<div class="f10">' . $rids[0]['kelas'] . '</div>';
$kelas_juara2 = !$global ? '' : '<div class="f10">' . $rids[1]['kelas'] . '</div>';
$kelas_juara3 = !$global ? '' : '<div class="f10">' . $rids[2]['kelas'] . '</div>';

$border1 = $my_rank == 1 ? 'border: solid 4px blue' : '';
$border2 = $my_rank == 2 ? 'border: solid 4px blue' : '';
$border3 = $my_rank == 3 ? 'border: solid 4px blue' : '';

$medal1st = '<div class="shimmer">&nbsp;1<sup class="shimmer-st"> st</sup></div>';
$medal2st = '<div class="shimmer shimmer-second">&nbsp;2<sup class="shimmer-st"> nd</sup></div>';
$medal3st = '<div class="shimmer shimmer-third">&nbsp;3<sup class="shimmer-st"> rd</sup></div>';

$medal1 = $dark ? $medal1st : '<img src=assets/img/gif/medal1-1.gif height=90px>';
$medal2 = $dark ? $medal2st : '<img src=assets/img/gif/medal2-1.gif height=90px>';
$medal3 = $dark ? $medal3st : '<img src=assets/img/gif/medal3-1.gif height=90px>';

echo "
  <div class='wadah gradasi-hijau mx-auto' style='max-width:500px'>

    <div class='wadah tengah juara-1' style='$border1'>
      $medal1
      <div class='darkblue mt2 f20'>$juara1$kelas_juara1</div>
      <div class=' darkblue '>$poin_juara1 LP</div>
    </div>


    <div class=row>
      <div class=col-6>
        <div class='wadah tengah juara-2' style='$border2'>
          $medal2
          <div class='darkblue mt1'>$juara2$kelas_juara2</div>
          <div class='kecil darkblue'>$poin_juara2 LP</div>
        </div>
      </div>
      <div class=col-6>
        <div class='wadah tengah juara-3' style='$border3'>
          $medal3
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

if ($id_role == 2) {
  // form reset leaderboard
  echo "
    <form method=post class='tengah wadah gradasi-kuning mx-auto' style='max-width:500px'>
      <button class='btn btn-danger' onclick='return confirm(`Reset Leaderboard di minggu ini?`)' name=btn_reset_leaderboard>Reset Leaderboard</button>
      <div class='mt1 f12 abu miring'>Jika ingin merekap ulang leaderboard di minggu ini silahkan Reset Leaderboard</div>
    </form>";
}















?>
<script>
  $(function() {

  })
</script>