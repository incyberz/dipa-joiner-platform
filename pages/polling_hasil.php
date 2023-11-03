<style>
  .blok-bar{border: solid 1px #ccc;padding: 5px; display:grid; grid-template-columns: 150px 20px auto}
  .bar-batang{border: solid 1px #eee; border-radius: 5px; font-size: 8px}
  .zzz{border: solid 1px #eee}
</style>
<section id="pengajar" class="pengajar section-bg">
  <div class="container">
<?php
# ====================================================================
// instruktur_only(); // ZZZ DISABLED

echo "
<div class='section-title' data-aos='fade'>
  <h2>Hasil Polling</h2>
</div>
";

# =========================================================
# GET POLLING DATA
# =========================================================
$s = "SELECT * FROM tb_polling WHERE untuk='uts' ORDER BY no,id";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$rpolling = [];
while ($d=mysqli_fetch_assoc($q)) {
  $rpolling[$d['no']] = [$d['pertanyaan'], $d['respon']];
  $poll[$d['no']][1] = 0;
  $poll[$d['no']][2] = 0;
  $poll[$d['no']][3] = 0;
  $poll[$d['no']][4] = 0;
  $poll[$d['no']][5] = 0;
}

# =========================================================
# GET POLLING ANSWER
# =========================================================
$s = "SELECT a.id as id_peserta, a.* FROM tb_polling_answer a";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_responden = mysqli_num_rows($q);
$ranswers = [];
$sarans = '';
while ($d=mysqli_fetch_assoc($q)){

  if(strlen($d['saran'])>10) $sarans.= "<div>$d[nama_responden] ~ $d[saran]</div> ~ $d[tanggal]";
  // $ranswers[$d['id_peserta']] = $d['jawabans'];
  // $ran[$d['id_peserta']] = explode('|',$d['jawabans']);
  $ran = explode('|',$d['jawabans']);
  foreach ($ran as $no_an) { //loop in 7x 
    if(strlen($no_an)>2){
      $rno_an = explode('-',$no_an);
      $no = $rno_an[0];
      $an = $rno_an[1];
      // $rno[$no]++;
      // $poll[$no][$an]++;
      $poll[$no][$an]++;

    }
  }
}

$sarans = $sarans=='' ? '<div class="kecil miring abu">(belum ada saran dan masukan)</div>' : $sarans;

// echo '<pre>';
// var_dump($ran);
// echo '</pre>';



# =========================================================
# FOREACH POLLING
# =========================================================
$count_rpolling = count($rpolling);
$polls = "<span class=debug><span id=jumlah_jawabans>$count_rpolling</span></span>";
foreach ($rpolling as $no => $rtanya) {
  $polls.="<span class=debug><span class=jawabans id=jawabans__$no></span></span>";

  $count[1] = $poll[$no][1];
  $count[2] = $poll[$no][2];
  $count[3] = $poll[$no][3];
  $count[4] = $poll[$no][4];
  $count[5] = $poll[$no][5];

  if($rtanya[1]=='rate'){
    //rate
    $opsi = '';
    $opsies = '';
    for ($i=1; $i <=5 ; $i++) { 
      $persen = round(100*$count[$i]/$jumlah_responden,0);
      $green = $i*50;
      $red = (5-$i)*50;
      $bg = "rgb($red,$green,100)";
      $no_counter = $no."__$i";
      $opsi .= "<img src=assets/img/icons/stars.png height=20px>";
      $opsies .= "
      <div class=kecil style='display:grid;grid-template-columns: 100px 30px auto'>
        <div class=right>$opsi</div>
        <div class=tengah>$count[$i]</div>
        <div class=zzz style='padding: 5px'><span style='display: inline-block; background:$bg; height: 100%; width: $persen%'></span></div>
      </div>
      ";
    }
    $opsi = "<div class='tengah mt2 mb4'>$opsi</div>";
    $opsi = $opsies;
  }else{
    // setuju / tdk setuju
    $width[1] = round(($count[1]/$jumlah_responden)*100,0);
    $width[2] = round(($count[2]/$jumlah_responden)*100,0);
    $width[3] = round(($count[3]/$jumlah_responden)*100,0);
    $width[4] = round(($count[4]/$jumlah_responden)*100,0);

    $red1 = 200;
    $red2 = 150;
    $red3 = 100;
    $red4 = 50;
    $green1 = 100;
    $green2 = 150;
    $green3 = 200;
    $green4 = 255;
    $start1 = 'fcc';
    $start2 = 'ffc';
    $start3 = 'ddf';
    $start4 = 'afa';
    if($no==3 || $no==4){
      $red4 = 200;
      $red3 = 150;
      $red2 = 100;
      $red1 = 50;
      $green4 = 100;
      $green3 = 150;
      $green2 = 200;
      $green1 = 255;
      $start4 = 'fcc';
      $start3 = 'ffc';
      $start2 = 'ddf';
      $start1 = 'afa';      
    }


    $opsi = "
      <div class='mt2 mb4 kecil'>
        <div class='mb2 blok-bar'>
          <div>Tidak $rtanya[1]</div>
          <div>$count[1]</div>
          <div class='bar-batang' style='width: $width[1]%; background: linear-gradient(to right, #$start1, rgb($red1,$green1,100))'></div>
        </div>
        <div class='mb2 blok-bar'>
          <div>Sedikit $rtanya[1]</div>
          <div>$count[2]</div>
          <div class='bar-batang' style='width: $width[2]%; background: linear-gradient(to right, #$start2, rgb($red2,$green2,100))'></div>
        </div>
        <div class='mb2 blok-bar'>
          <div>Saya $rtanya[1]</div>
          <div>$count[3]</div>
          <div class='bar-batang' style='width: $width[3]%; background: linear-gradient(to right, #$start3, rgb($red3,$green3,100))'></div>
        </div>
        <div class='mb2 blok-bar'>
          <div>Sangat $rtanya[1]</div>
          <div>$count[4]</div>
          <div class='bar-batang' style='width: $width[4]%; background: linear-gradient(to right, #$start4, rgb($red4,$green4,100))'></div>
        </div>
      </div>
    ";


  }

  // show pertanyaan
  $polls.= "
    <div class='btop pt2 mb2' id=polls__$no>
      $no
      <div class=mb2>$rtanya[0]</div>
      $opsi
    </div>
  ";
}

echo "
  <h4 class='darkblue mb2 mt2'>Polling:</h4>
  $polls
  <div class='form-group mb2 mt2' id=blok_saran>
    <h4 class='darkblue'><label for='saran'>Saran dan masukan:</label></h4>
    $sarans
  </div>
";













# ====================================================================
?>
  </div>
</section>