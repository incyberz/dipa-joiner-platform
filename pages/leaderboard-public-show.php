<?php
$my_rank = null;
if ($id_role == 1) {
  // $my_rank = $rranks
}


$juara1 = ucwords(strtolower($rranks[0]['nama']));
$juara2 = ucwords(strtolower($rranks[1]['nama']));
$juara3 = ucwords(strtolower($rranks[2]['nama']));

$poin_juara1 = number_format($rranks[0]['poin']);
$poin_juara2 = number_format($rranks[1]['poin']);
$poin_juara3 = number_format($rranks[2]['poin']);

$kelas_juara1 = !$global ? '' : '<div class="f10">' . $rranks[0]['kelas'] . '</div>';
$kelas_juara2 = !$global ? '' : '<div class="f10">' . $rranks[1]['kelas'] . '</div>';
$kelas_juara3 = !$global ? '' : '<div class="f10">' . $rranks[2]['kelas'] . '</div>';

for ($i = 0; $i < 3; $i++) {
  $image = "$lokasi_profil/" . $rranks[$i]['image'];
  $war_image = "$lokasi_profil/" . $rranks[$i]['war_image'];
  if (file_exists($war_image)) {
    $rsrc[$i] = $war_image;
  } elseif (file_exists($image)) {
    $rsrc[$i] = $image;
  } else {
    $rsrc[$i] = $src_profil_na;
  }
}

$juara1 = "<img src='$rsrc[0]' class='profil_pembuat'><br>$juara1";
$juara2 = "<img src='$rsrc[1]' class='profil_pembuat'><br>$juara2";
$juara3 = "<img src='$rsrc[2]' class='profil_pembuat'><br>$juara3";

# ============================================================
# JUARA 4 DST
# ============================================================
$juara4 = '';
$btop = '';
foreach ($rranks as $k => $arr) {
  if ($k < 3) continue;
  if ($k > 3) $btop = 'btop';
  $rank = $rranks[$k]['rank'];
  $poin = number_format($rranks[$k]['poin'], 0);
  $nama = ucwords(strtolower($arr['nama']));
  $kelas = $global ? "<div class='f10 abu'>$arr[kelas]</div>" : '';
  $my_row = $arr['id'] == $id_peserta ? 'border-top: solid 5px #ccf;border-bottom: solid 5px #ccf; background: linear-gradient(#efe,#ffc); margin: 0 -12px 0 -12px;padding:12px' : '';
  $juara4 .= "
    <div class='flexy $btop ' style='$my_row'>
      <div class='mt1 mb1' style='flex: 1'>$rank <span class='rank_th f10'>th</span></div>
      <div class='mt1 mb1' style='flex: 3'>$nama$kelas</div>
      <div class='mt1 mb1' style='flex: 2'>$poin <span class='kecil miring abu'>LP</span></div>
    </div>
  ";
}







$border1 = $my_rank == 1 ? 'border: solid 4px blue' : '';
$border2 = $my_rank == 2 ? 'border: solid 4px blue' : '';
$border3 = $my_rank == 3 ? 'border: solid 4px blue' : '';

$silahkan_login = $username ? '' : "<div class='tengah f12 '>Silahkan <a href=?login>Login</a> untuk melihat Rank kamu atau ranking lainnya.</div>";


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

    $silahkan_login


    
  </div>
";
