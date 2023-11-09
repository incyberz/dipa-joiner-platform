<section id="about" class="about"><div class="container">
<?php
# =================================================================
login_only();

$link = "<a href='?tanam_soal'>Tanam Soal</a>";
$link2 = "<a href='?soal_saya'>Soal Saya</a>";
$link3 = "<a href='?perang_soal'>Perang Home</a>";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>War History</h2>
    <p>
      <div>$link3 | $link2</div>
      <div class='kecil mt2 abu'>Jejak perang yang tak terlupakan.</div> 
    </p>
  </div>
";

$s = "SELECT a.*,
a.id as id_perang,
b.username as pembuat,
c.id_status as id_status_soal   
FROM tb_perang a 
JOIN tb_peserta b ON a.id_pembuat=b.id 
JOIN tb_soal_pg c ON a.id_soal=c.id 

WHERE a.id_penjawab='$id_peserta'

ORDER BY a.tanggal DESC 
LIMIT 20
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  $meme = meme('dont-have');
  echo div_alert('danger tengah', "<div class=mb2>Lo ga pernah ikut perang jehh!!</div>$meme");
}else{
  $div = '';
  $profil = "assets/img/peserta/wars/peserta-$id_peserta.jpg";
  if(file_exists($profil)){
    $profil = "<img class=profil_penjawab src='$profil' />";
  }else{
    $profil = "<img class=profil_penjawab src='assets/img/no_war_profil.jpg' />";
  }

  while($d=mysqli_fetch_assoc($q)){
    $id_perang=$d['id_perang'];
    $id_pembuat=$d['id_pembuat'];
    $is_benar=$d['is_benar'];
    $r = rand(1,12);
    $cermin = '';


    if($is_benar==1){
      $gradasi = 'hijau';
    }elseif($is_benar==-1){
      $gradasi = 'kuning';
      $r = 0;
    }else{
      $gradasi = 'merah';
      $cermin = 'cermin';
    }

    $profil2 = "assets/img/peserta/wars/peserta-$id_pembuat.jpg";
    if(file_exists($profil2)){
      $profil2 = "<img class=profil_penjawab src='$profil2' />";
    }else{
      $profil2 = "<img class=profil_penjawab src='assets/img/no_war_profil.jpg' />";
    }

    $tanggal = date('M d, Y, H:i:s', strtotime($d['tanggal']));

    $div .= "
      <div class='btop gradasi-$gradasi pb2'>
        <div class='miring abu f10px mt2 mb1 tengah'>$tanggal</div>
        <div class='row'>
          <div class='col-4 tengah kecil'>
            $profil
            <br>You
            <br><span class='miring abu f10'>$d[poin_penjawab] LP</span>
          </div>
          <div class='col-4 tengah kecil pt4'><img src='assets/img/guns/wp$r.png' class='$cermin' style='max-width:70px' /></div>
          <div class='col-4 tengah kecil'>
            $profil2
            <br>$d[pembuat]
            <br><span class='miring abu f10'>$d[poin_pembuat] LP</span>
          </div>
        </div>  
      </div>  
    ";
  }

  echo "<div style='max-width:500px; margin:auto'>$div</div>";

}



















?></div></section>
<script>
  $(function(){

  })
</script>
