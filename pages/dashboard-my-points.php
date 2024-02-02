<?php
$div_wars = '';
$div_kbm = '';
$total_points =0;
$unset = '<span class="consolas f12 abu miring">-</span>';

# ==========================================================
# DATA WARS
# ==========================================================
$s = "DESCRIBE tb_war_summary";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$Field = [];
while($d=mysqli_fetch_assoc($q)){
  array_push($Field,$d['Field']);
}

$s = "SELECT * FROM tb_war_summary WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if($d){
  $tr_war = '';
  $war_points = $d['war_points'];
  $total_points += $war_points;
  foreach ($Field as $kolom) {
    if($kolom=='id' 
      || $kolom=='id_peserta'
      || $kolom=='id_room'
    ) continue;
    $value = $d[$kolom] ;
    $kolom_show = ucwords(str_replace('_',' ',$kolom));
    $tr_war .= "
      <tr>
        <td>$kolom_show</td>
        <td>$value</td>
      </tr>
    ";
  }

  $img = img_icon('detail');

  $div_wars = "
    <h5 class='card-title'>War Points : $d[war_points] LP <span class=btn_aksi id=war_points__toggle>$img</span></h5>
    <div class='wadah gradasi-hijau hideit' id=war_points>
      <table class='table table-striped f12'>
        <div class=mb2>War Point Details</div>
        $tr_war
      </table>
    </div>
  ";
}else{
  $div_wars = div_alert('info','Belum ada Wars pada room ini.');
}

# ==========================================================
# POIN KBM
# ==========================================================
$s = "DESCRIBE tb_poin";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$Field = [];
while($d=mysqli_fetch_assoc($q)){
  array_push($Field,$d['Field']);
}

$s = "SELECT * FROM tb_poin WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if($d){
  $total_points += $d['akumulasi_poin'];
  $tr_kbm = '';
  foreach ($Field as $kolom) {
    if($kolom=='id' 
      || $kolom=='id_peserta'
      || $kolom=='id_room_kelas'
      || $kolom=='id_room'
    ) continue;
    $value = $d[$kolom];
    if($value==''){
      $value_show = $unset;
    }else{
      $value_show = $value;
    } 
    $kolom_show = ucwords(str_replace('_',' ',$kolom));
    if($kolom_show=='Uts'
      || $kolom_show=='Uas'
    ) $kolom_show = strtoupper($kolom_show);
    $tr_kbm .= "
      <tr>
        <td>$kolom_show</td>
        <td>$value_show</td>
      </tr>
    ";
  }

  $div_kbm = "
    <h5 class='card-title'>Point KBM : $my_points LP</h5>
    <div class='wadah '>
      <table class='table table-striped f12'>
        <div class=mb2>Detail Nilai KBM</div>
        $tr_kbm
      </table>
    </div>
  ";
}else{
  $div_kbm = div_alert('info','Belum ada Nilai KBM pada room ini.');
}









?>
<div class="card mb2 p2">
  <h5 class="card-title">My Points</h5>
  <div class='my_points'><?=$total_points?> LP</div>
  <p class="small fst-italic">Poin didapatkan dari seluruh aktifitas pembelajaran semisal latihan, challenge, bertanya, menjawab (chats), Ujian, dan Wars.</p>

  <?=$div_wars?>
  <?=$div_kbm?>
  
</div>