<?php
if($dm)
echo "
  <div class='debug'>
    <h1>Debugging Grades</h1>
    <br>kelas: $kelas
    <br>id_room_kelas: $id_room_kelas
  </div>
";
// login_only(); // boleh tidak login
$get_kelas = $_GET['kelas'] ?? '';
if(($get_kelas=='all' and $id_role==1) || $get_kelas=='') die("<script>location.replace('?grades&kelas=$kelas')</script>");
$judul = 'The Best Top 10';
$img_login_as = '<img src="assets/img/icons/login_as.png" height=20px class=zoom>';
$show_img = isset($_GET['show_img']) ? $_GET['show_img'] : 0;

if($kelas!='all'){
  include 'include/arr_kelas.php';
  foreach ($arr_kelas as $kls => $jml) {
    $arr_rank_kelas[$kls] = 0;
    $jumlah_peserta_kelas[$kls] = $jml;
  }
}else{
  $arr_rank_kelas[$kelas] = 0;
}

$s = "SELECT a.last_update_point FROM tb_poin a JOIN tb_peserta b ON a.id_peserta=b.id ORDER BY a.last_update_point DESC LIMIT 1 ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  // belum ada data poin
  $s2 = "INSERT INTO tb_poin (id_peserta,id_room_kelas) VALUES ($id_peserta,$id_room_kelas)";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  jsurl();
  exit;
}

$d = mysqli_fetch_assoc($q);
$last_update_point = $d['last_update_point'] ?? '';
$selisih = $id_role==2 ? 600 : (strtotime('now') - strtotime($last_update_point));
if($selisih>=600 and $id_role!=3 and $is_login){

  // reupdate grades
  echo div_alert('info','Reupdate Grades ... please wait!');
  $s = "SELECT 
    a.id, 
    (
      SELECT poin_presensi FROM tb_presensi_summary   
      WHERE id_peserta=a.id 
      AND id_room=$id_room  
      ) as poin_presensi,
    (
      SELECT war_points FROM tb_war_summary   
      WHERE id=a.id 
      AND id_room = $id_room  
      ) as war_points,
    (
      SELECT COUNT(1) FROM tb_pertanyaan  
      WHERE id_penanya=a.id 
      AND id_room_kelas = $id_room_kelas  
      ) as count_bertanya,
    (
      SELECT COUNT(1) FROM tb_pertanyaan_reply  
      WHERE id_penjawab=a.id 
      AND id_room_kelas = $id_room_kelas  
      ) as count_menjawab,
    (
      SELECT SUM(poin) FROM tb_pertanyaan  
      WHERE id_penanya=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null 
      ) as poin_bertanya,
    (
      SELECT SUM(poin) FROM tb_pertanyaan_reply  
      WHERE id_penjawab=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null 
      ) as poin_menjawab,
    (
      SELECT SUM(p.get_point) FROM tb_bukti_latihan p 
      JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND p.tanggal_verifikasi is not null 
      AND status=1
      ) as total_poin_latihan,
    (
      SELECT SUM(p.get_point) FROM tb_bukti_challenge p 
      JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND p.tanggal_verifikasi is not null 
      AND status=1
      ) as total_poin_challenge



  
  FROM tb_peserta a 
  ";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while ($d=mysqli_fetch_assoc($q)) {
    // echo "<hr>$d[id] :: $d[total_poin_latihan]";
    $poin_presensi = $d['poin_presensi'] ?? 0;
    $war_points = $d['war_points'] ?? 0;
    $count_bertanya = $d['count_bertanya'] ?? 0;
    $count_menjawab = $d['count_menjawab'] ?? 0;
    $poin_bertanya = $d['poin_bertanya'] ?? 0;
    $poin_menjawab = $d['poin_menjawab'] ?? 0;
    $total_poin_latihan = $d['total_poin_latihan'] ?? 0;
    $total_poin_challenge = $d['total_poin_challenge'] ?? 0;

    $total_poin_bertanya = $poin_bertanya + $count_bertanya*100;
    $total_poin_menjawab = $poin_menjawab + $count_menjawab*10;
    $total_poin = 0
                  +$poin_presensi
                  +$war_points 
                  +$total_poin_bertanya
                  +$total_poin_menjawab
                  +$total_poin_latihan
                  +$total_poin_challenge;


    if($dm and $d['id']==51) echo "<hr>
    total_poin: $total_poin = 0
                  +poin_presensi: $poin_presensi
                  +war_points: $war_points 
                  +total_poin_bertanya: $total_poin_bertanya
                  +total_poin_menjawab: $total_poin_menjawab
                  +total_poin_latihan: $total_poin_latihan
                  +total_poin_challenge: $total_poin_challenge;
    ";
                  
    $s2 = "UPDATE tb_poin SET 
    akumulasi_poin=$total_poin,
    poin_bertanya=$total_poin_bertanya,
    poin_menjawab=$total_poin_menjawab,
    poin_latihan=$total_poin_latihan,
    poin_challenge=$total_poin_challenge,
    last_update_point=CURRENT_TIMESTAMP 
    WHERE id_peserta=$d[id] ";
    // echo '<pre>';
    // var_dump($s2);
    // echo '</pre>';
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  }
  
}

$limit = $id_role<=1 ? 'LIMIT 10' : '';
$only_peserta = $id_role<=1 ? ' a.id_role = 1' : '1';
$sql_kelas = ($get_kelas=='' || $get_kelas=='all') ? '1' : "b.kelas = '$get_kelas'";

$s = "SELECT 
a.id as id_peserta,
a.nama, 
a.username, 
b.* , 
c.akumulasi_poin,
(
  SELECT count(1) FROM tb_peserta p 
  JOIN tb_kelas_peserta q ON q.id_peserta=p.id 
  WHERE p.status=1 AND q.kelas=b.kelas) jumlah_peserta_kelas  

FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_poin c ON c.id_peserta=a.id 
WHERE $only_peserta 
AND a.status=1 
AND $sql_kelas
ORDER BY c.akumulasi_poin DESC $limit";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$tb = div_alert('danger', 'Belum ada data peserta.');
if(mysqli_num_rows($q)){
  $tr = '';
  $i=0;
  $my_rank = 0;
  $jumlah_rows = mysqli_num_rows($q);
  while ($d=mysqli_fetch_assoc($q)) {
    $i++;
    $arr_rank_kelas[$d['kelas']]++;
    $nama_show = ucwords(strtolower($d['nama']));
    $poin_show = number_format($d['akumulasi_poin'],0);

    # ==================================
    # MY RANK
    # ==================================
    if(strtolower($username)==strtolower($d['username'])){
      $tr_sty = "border: solid 3px blue; font-weight:bold";
      $my_rank = $i;
      $link_nama_show = "<a href='?upload_profil'>$nama_show</a>";
      $link_point_show = "<a href='?my_points'>$poin_show LP</a>";
    }else{
      $link_nama_show = "$nama_show";
      $link_point_show = "$poin_show LP";
      $tr_sty = '';
    }

    //autosave rank
    if($id_role==2){
      $rank_kelas = $arr_rank_kelas[$d['kelas']];
      $jp_kelas = $jumlah_peserta_kelas[$d['kelas']];

      $s2 = "UPDATE tb_poin SET rank_global='$i',rank_kelas='$rank_kelas' WHERE id=$d[id_peserta]";
      // die($s2);
      $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    }

    $login_as = $id_role==2 ? "<a href='?login_as&username=$d[username]' target=_blank>$img_login_as</a>" : '';
    $td_profil = ($id_role==2 and $show_img) ? "<td><img src='assets/img/peserta/peserta-$d[id].jpg' class=foto_profil></td>" : '';

    $toggle_profil = $id_role==2 ? "<a href='?grades&show_img=1'>Show Profile</a>" : '';
    $toggle_profil = ($id_role==2 and $show_img) ? "<a href='?grades'>Hide Profile</a>" : $toggle_profil;

    $tr .= "
      <tr style='$tr_sty'>
        <td>$i</td>
        $td_profil
        <td>$link_nama_show $login_as <div class='kecil darkred'>$d[kelas]</div></td>
        <td>$link_point_show</td>
      </tr>
    ";
  }
  $selamat = $my_rank ? div_alert('success',"Selamat! Kamu berada di Ranking $my_rank dari 10 terbaik.") 
  : div_alert('warning','Wah sepertinya kamu harus belajar lebih giat agar berada di 10 terbaik.');
  $selamat = ($is_login && $id_role==1) ? $selamat : '';
  $tb = "$selamat<div class='mb2  '>$toggle_profil</div><table class='table table-striped table-hover'>$tr</table>";
}

$kelas_show = $get_kelas=='' ? 'Semua Kelas' : "Kelas $get_kelas";
if($id_role==2 && $get_kelas!='all'){
  $kelas_show.= ' | <a href="?grades&kelas=all">All Kelas</a>';
}else{
  $s = "SELECT kelas from tb_kelas WHERE tahun_ajar=$tahun_ajar";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while ($d=mysqli_fetch_assoc($q)) {
    $kelas_show.= " | <a href='?grades&kelas=$d[kelas]'>$d[kelas]</a>";
  }
}
?>


<!-- <div class="section-title" data-aos="fade-up">
  <h2>Grades</h2>
</div> -->

<h4 class='darkblue bold text-center consolas ' data-aos="fade-up" data-aos-delay="150"><?=$judul?></h4>
<div class='darkblue text-center consolas mb-2 f18' data-aos="fade-up" data-aos-delay="150">
  <?=$nama_room?>
</div>
<div class="grades" data-aos="fade-up" data-aos-delay="150">
  <p>Berikut adalah 10 Peserta Terbaik <span class="darkred"><?=$kelas_show?></span>.</p>

  <?=$tb?>
  <div class="kecil miring abu">
    Poin didapatkan dari pengerjaan latihan secara ontime, posting pertanyaan berbobot, atau aktifitas belajar lainnya.
  </div>
</div>