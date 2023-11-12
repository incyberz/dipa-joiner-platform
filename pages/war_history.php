<section id="about" class="about"><div class="container">
<?php
# =================================================================
login_only();
include 'include/arr_kelas.php';
$no_war_profil = "<img class=profil_penjawab src='assets/img/no_war_profil.jpg' />";

$all_wars = isset($_GET['all_wars']) ? 1 : 0;
$get_kelas = $_GET['kelas'] ?? '';

$sql_my_wars = $all_wars ? '1' : "a.id_penjawab='$id_peserta'";
$limit = $all_wars ? '100' : '20';


$nav_kelas = '';
if($id_role!=1){
  //dosen only
  foreach ($arr_kelas as $kelas => $jml) {
    if($kelas=='BOCIL') continue;
    $nav_kelas.= "<a href='?war_history&all_wars&kelas=$kelas'>$kelas</a> | ";
  }
  $nav_kelas = "<div class=kecil>$nav_kelas</div>";
  $sql_kelas = $get_kelas=='' ? 1 : "b.kelas = '$get_kelas'";
  $sql_my_wars = $all_wars ? '1' : "a.id_penjawab=$id_peserta";
}else{
  $sql_my_wars = $all_wars ? "b.kelas='$kelas'" : "a.id_penjawab=$id_peserta";
  $sql_kelas = '1';
  $nav_kelas = "<div class='kecil mt2 abu'>Jejak perang yang tak terlupakan.</div> ";
}

$link = "<a href='?tanam_soal'>Tanam Soal</a>";
$link2 = "<a href='?soal_saya'>Soal Saya</a>";
$link3 = "<a href='?perang_soal'>Perang Home</a>";
$link4 = "<a href='?war_history&all_wars'>All Wars</a>";
$link5 = "<a href='?war_history'>My Wars</a>";
$link6 = "<a href='?war_summary'>War Summary</a>";

$second_link = $all_wars ? $link5 : $link4;
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>War History</h2>
    <p>
      <div>$link3 | $second_link | $link6</div>
      $nav_kelas
    </p>
  </div>
";

$s = "SELECT a.*,
a.id as id_perang,
b.username as penjawab,
b.kelas as kelas_penjawab,
c.id_status as id_status_soal,
(SELECT username FROM tb_peserta WHERE id=a.id_pembuat) pembuat,   
(SELECT kelas FROM tb_peserta WHERE id=a.id_pembuat) kelas_pembuat   
FROM tb_perang a 
JOIN tb_peserta b ON a.id_penjawab=b.id 
JOIN tb_soal_pg c ON a.id_soal=c.id 

WHERE $sql_my_wars 
AND $sql_kelas

ORDER BY a.tanggal DESC 
LIMIT $limit
";
// echo "<pre>$s</pre>";
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
    $penjawab=$d['penjawab'];
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
      $profil2 = $no_war_profil;
    }

    $tanggal = date('M d, Y, H:i:s', strtotime($d['tanggal']));
    $eta_show = eta(-strtotime('now')+strtotime($d['tanggal']));

    $img_guns = "<img src='assets/img/guns/wp$r.png' style='max-width:70px' class='$cermin pt4'  />";

    $src_profil_penjawab = "assets/img/peserta/wars/peserta-$d[id_penjawab].jpg";
    $profil_penjawab = "<img class=profil_penjawab src='$src_profil_penjawab' />";
    if(!file_exists($src_profil_penjawab)) $profil_penjawab = $no_war_profil;

    if($all_wars){
      if($id_role==1) $profil2 = '';
      $profil = $id_role==1 ? '' : $profil_penjawab;
      $you = $penjawab;
      $guns = $d['poin_pembuat']=='' ? '<span class=red>tidak menjawab</span>' : '<span class=abu>menjawab salah</span>';
      $guns = $d['is_benar']==1 ? '<span class=blue>menjawab benar</span>' : $guns;
      $guns = $d['is_benar']==-1 ? '<span class=red>rejecting</span>' : $guns;
      $guns = $id_role==1 ? $guns : "$img_guns<div class=mt3>$guns</div>";
      $poin_penjawab_show = $id_role==1 ? "$d[poin_penjawab] LP" : "$d[kelas_penjawab] | $d[poin_penjawab] LP";
      $poin_pembuat_show = "$d[kelas_pembuat] | $d[poin_pembuat] LP";
    }else{
      $you = 'You';
      $guns = $img_guns;
      $poin_penjawab_show = "$d[poin_penjawab] LP";
      $poin_pembuat_show = "$d[poin_pembuat] LP";
    }

    $div .= "
      <div class='btop gradasi-$gradasi pb2'>
        <div class='miring abu f10px mt2 mb1 tengah'> $eta_show</div>
        <div class='row'>
          <div class='col-4 tengah kecil'>
            $profil
            <div>$you</div>
            <div><span class='miring abu f10'>$poin_penjawab_show</span></div>
          </div>
          <div class='col-4 tengah kecil'>$guns</div>
          <div class='col-4 tengah kecil'>
            $profil2
            <div>$d[pembuat]</div>
            <div><span class='miring abu f10'>$poin_pembuat_show</span></div>
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
