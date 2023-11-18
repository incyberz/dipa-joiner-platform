<?php 
$today=date('Y-m-d');
$undef = '<span class="red kecil miring">undefined</span>';

# ========================================================
# GET DATA PESERTA
# ========================================================
$s = "SELECT a.*, b.sebagai,
(SELECT 1 FROM tb_biodata WHERE id_peserta=a.id) punya_biodata ,
(
  SELECT count(1) FROM tb_peserta WHERE status=1 AND kelas=a.kelas) total_kelas_ini 
FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.username='$username'";
$sd = $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("mhs_var :: Mahasiswa dengan Username: $username tidak ditemukan.");
$d_peserta = mysqli_fetch_assoc($q);

$folder_uploads = $d_peserta['folder_uploads'];
$id_role = $d_peserta['id_role'];
if($folder_uploads==''){
  $a = '_'.strtolower($d_peserta['nama']);
  $a = str_replace(' ','',$a);
  $a = str_replace('.','',$a);
  $a = str_replace(',','',$a);
  $a = str_replace('\'','',$a);
  $a = str_replace('`','',$a);
  $a = substr($a,0,6).date('ymdHis');

  $folder_uploads = $a;
  $ss = "UPDATE tb_peserta set folder_uploads='$a' where username='$username'";
  $qq = mysqli_query($cn,$ss)or die("Update folder_uploads error. ".mysqli_error($cn));
}

if(!file_exists("uploads/$folder_uploads")) mkdir("uploads/$folder_uploads");



$id_peserta = $d_peserta['id'];
$nama = $d_peserta['nama'];
$kelas = $d_peserta['kelas'];
$sebagai = $d_peserta['sebagai'];
$nama_peserta = $d_peserta['nama'];
$nama_peserta = ucwords(strtolower($nama_peserta));
$no_wa = $d_peserta['no_wa']!=''?$d_peserta['no_wa']:'';
$no_wa_show = $no_wa==''?$undef:substr($no_wa,0,4).'***'.substr($no_wa,strlen($no_wa)-3,3);
$akumulasi_poin = $d_peserta['akumulasi_poin'];
$password = $d_peserta['password'];
$status = $d_peserta['status'];
$punya_biodata = $d_peserta['punya_biodata'];
$is_depas = $password=='' ? 1 : 0;

$my_points = number_format($akumulasi_poin,0);
$uts = $d_peserta['uts'] ?? 0;
$uas = $d_peserta['uas'] ?? 0;
$nilai_akhir = $d_peserta['nilai_akhir'];
$rank_kelas = $d_peserta['rank_kelas'];
$rank_global = $d_peserta['rank_global'];
$total_kelas_ini = $d_peserta['total_kelas_ini'];
$profil_ok = $d_peserta['profil_ok'];

$total_kelas_peserta = $total_kelas_ini;





# =======================================================
# COUNT TOTAL LATIHAN / TUGAS / CHALLENGE
# =======================================================
$s = "SELECT 
(SELECT COUNT(1) FROM tb_peserta WHERE status=1) total_peserta,
(SELECT COUNT(1) FROM tb_act_latihan WHERE status=1) total_latihan,
(SELECT COUNT(1) FROM tb_act_tugas WHERE status=1) total_tugas,
(SELECT COUNT(1) FROM tb_act_challenge WHERE status=1) total_challenge
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$total_peserta = $d['total_peserta'];
$total_latihan = $d['total_latihan'];
$total_tugas = $d['total_tugas'];
$total_challenge = $d['total_challenge'];



# ========================================================
# PROFILE HANDLER
# ========================================================
$path_profil = "assets/img/peserta/peserta-$id_peserta.jpg";
// echo "<h1 style='padding-top:200px'>$path_profil</h1>";
$rand = rand(1,5);
$path_profil_na = "assets/img/no_profile$rand.jpg";
if(file_exists($path_profil)){
  $punya_profil = true;
}else{
  $punya_profil = false;
  $path_profil = $path_profil_na;
}


# ========================================================
# PROFIL PERANG
# ========================================================
$path_profil_perang = "assets/img/peserta/wars/peserta-$id_peserta.jpg";
$path_profil_perang_na = $path_profil_na;
if(file_exists($path_profil_perang)){
  $punya_profil_perang = true;
}else{
  $punya_profil_perang = false;
  $path_profil_perang = $path_profil_perang_na;
}


# ========================================================
# RANK KELAS DAN JUMLAH PESERTA
# ========================================================
if($rank_kelas==''){
  $s = "SELECT a.id,  
  RANK() over (ORDER BY a.akumulasi_poin DESC) rank
  FROM tb_peserta a 
  WHERE a.status=1 
  AND a.kelas='$kelas'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $jumlah_peserta = mysqli_num_rows($q);
  $ranks = [];
  while ($d=mysqli_fetch_assoc($q)) {
    $ranks[$d['id']] = $d['rank'];
  }
  $rank_kelas = $ranks[$id_peserta];
}

if($rank_kelas){
  if($rank_kelas%10==1){
    $th = 'st';
  }elseif($rank_kelas%10==2){
    $th = 'nd';
  }elseif($rank_kelas%10==3){
    $th = 'rd';
  }else{
    $th = 'th';
  }
}else{
  $rank_kelas = '?';
  $th = '';
}

# ============================================================
# AVERAGE DAN NILAI AKHIR
# ============================================================
$s = "SELECT AVG(akumulasi_poin) as average FROM tb_peserta WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$average = $d['average'];
$nilai_akhir = $nilai_akhir=='' ? round(($akumulasi_poin / ($average*1.2)) *100,0) : $nilai_akhir;
if($nilai_akhir>100) $nilai_akhir=100;
if($nilai_akhir==0){
  $hm = 'B';
}elseif($nilai_akhir>=85){
  $hm = 'A';
}elseif($nilai_akhir>=70){
  $hm = 'B';
}elseif($nilai_akhir>=60){
  $hm = 'C';
}elseif($nilai_akhir>=40){
  $hm = 'D';
}else{
  $hm = 'E';
}









$pekerjaan = '';
$testimony = '';
$alamat = '';
$desa = '';
$kec = '';
$kab = '';
if($punya_biodata){
  $s = "SELECT * FROM tb_biodata WHERE id_peserta=$id_peserta";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $d=mysqli_fetch_assoc($q);
    $pekerjaan = $d['pekerjaan'];
    $testimony = $d['testimony'];
    $alamat = $d['alamat'];
    $desa = $d['desa'];
    $kec = $d['kec'];
    $kab = $d['kab'];
  }
}







# ============================================================
# AUTO-SELF UPDATE EVERY HOUR | AVAILABLE SOAL
# ============================================================
$selisih = $id_role==1 ? (strtotime('now') - strtotime($d_peserta['last_update_available_soal'])) : 0;
$available_soal = $d_peserta['available_soal'];
if($selisih>3600 || $d_peserta['available_soal']==''){
  $s = "SELECT a.id FROM tb_soal_pg a 
  LEFT JOIN tb_perang b ON a.id=b.id_soal AND b.id_penjawab=$id_peserta 
  WHERE (a.id_status is null OR a.id_status >= 0) 
  AND b.id is null 
  AND a.id_pembuat!=$id_peserta 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $available_soal = mysqli_num_rows($q);

  $s = "UPDATE tb_peserta SET last_update_available_soal=CURRENT_TIMESTAMP, available_soal=$available_soal WHERE id=$id_peserta 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  // die('UPDATED');
}

# =========================================================
# GET TMP WAR DATA
# =========================================================
$last_update_war_tmp = '';
$must_update = 0;

$war_rank = 0;
$war_points = 0;
$war_point_quiz = 0;
$war_point_reject = 0;
$war_point_passive = 0;

$accuracy = 0;

$count_answer = 0;
$count_answer_right = 0;
$count_answer_false = 0;
$count_reject = 0;
$count_not_answer = 0;

$my_question_banned = 0;
$my_question_unverified = 0;
$my_question_verified = 0;
$my_question_decided = 0;
$my_question_promoted = 0;

$available_questions = 0;

$s = "SELECT * FROM tb_perang_summary WHERE id=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){
  $d=mysqli_fetch_assoc($q);

  $war_rank = $d['war_rank'];
  $war_points = $d['war_points'];
  $war_point_quiz = $d['war_point_quiz'];
  $war_point_reject = $d['war_point_reject'];
  $war_point_passive = $d['war_point_passive'];

  $count_answer_right = $d['count_answer_right'];
  $count_answer_false = $d['count_answer_false'];
  $count_reject = $d['count_reject'];
  $count_not_answer = $d['count_not_answer'];

  $my_question_banned = $d['my_question_banned'];
  $my_question_unverified = $d['my_question_unverified'];
  $my_question_verified = $d['my_question_verified'];
  $my_question_decided = $d['my_question_decided'];
  $my_question_promoted = $d['my_question_promoted'];

  $available_questions = $d['available_questions'];
  $poin_membuat_soal = $d['poin_membuat_soal'];
  $poin_tumbuh_soal = $d['poin_tumbuh_soal'];
  
  $selisih_war = strtotime('now')-strtotime($d['last_update']);
  if($selisih_war>3600){
    $must_update = 1; // if > 1 jam must update
  }

}else{
  // jika belum punya data war summary
  $must_update = 1;
  $s = "INSERT INTO tb_perang_summary (id, last_update) VALUES ($id_peserta,'2020-1-1')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}

# =========================================================
# GET REALTIME WAR DATA
# =========================================================
if($must_update){

  $from_perang = "FROM tb_perang p WHERE p.id_penjawab=$id_peserta";
  $from_soal = "FROM tb_soal_pg s WHERE s.id_pembuat=$id_peserta";

  $s = "SELECT 
  (SELECT last_update FROM tb_perang_summary WHERE id=$id_peserta ) as last_update,
  (SELECT SUM(poin_penjawab) $from_perang AND p.is_benar >= 0) as war_point_quiz,
  (SELECT SUM(poin_penjawab) $from_perang AND p.is_benar < 0) as war_point_reject,

  (SELECT COUNT(1) $from_perang AND p.is_benar = 1) as count_answer_right,
  (SELECT COUNT(1) $from_perang AND p.is_benar = 0) as count_answer_false,
  (SELECT COUNT(1) $from_perang AND p.is_benar < 0) as count_reject,
  (SELECT COUNT(1) $from_perang AND p.is_benar is null ) as count_not_answer,

  (SELECT COUNT(1) $from_soal AND s.id_status < 0) as my_question_banned,
  (SELECT COUNT(1) $from_soal AND (s.id_status = 0 OR s.id_status is null)) as my_question_unverified,
  (SELECT COUNT(1) $from_soal AND s.id_status = 1) as my_question_verified,
  (SELECT COUNT(1) $from_soal AND s.id_status = 2) as my_question_decided,
  (SELECT COUNT(1) $from_soal AND s.id_status = 3) as my_question_promoted,

  (SELECT SUM(s.poin_membuat_soal) $from_soal) as poin_membuat_soal,
  (SELECT SUM(poin_pembuat) FROM tb_perang p WHERE p.id_pembuat=$id_peserta ) as poin_tumbuh_soal,

  (
    SELECT COUNT(1) FROM tb_soal_pg a 
    LEFT JOIN tb_perang b ON a.id=b.id_soal AND b.id_penjawab=$id_peserta 
    WHERE (a.id_status is null OR a.id_status >= 0) 
    AND b.id is null 
    AND a.id_pembuat!=$id_peserta ) as available_questions

  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $war_point_quiz = $d['war_point_quiz'];
  $war_point_reject = $d['war_point_reject'];
  $poin_tumbuh_soal = $d['poin_tumbuh_soal'];
  $poin_membuat_soal = $d['poin_membuat_soal'];
  
  $war_point_passive = $poin_membuat_soal + $poin_tumbuh_soal;

  $count_answer_right = $d['count_answer_right'];
  $count_answer_false = $d['count_answer_false'];
  $count_reject = $d['count_reject'];
  $count_not_answer = $d['count_not_answer'];

  $my_question_banned = $d['my_question_banned'];
  $my_question_unverified = $d['my_question_unverified'];
  $my_question_verified = $d['my_question_verified'];
  $my_question_decided = $d['my_question_decided'];
  $my_question_promoted = $d['my_question_promoted'];

  $available_questions = $d['available_questions'];
  $last_update = $d['last_update'];

  $war_points = $war_point_quiz + $war_point_reject + $poin_membuat_soal + $poin_tumbuh_soal;

  $s = "UPDATE tb_perang_summary SET 
    war_points = '$war_points',
    war_point_quiz = '$war_point_quiz',
    war_point_reject = '$war_point_reject',
    war_point_passive = '$war_point_passive',

    count_answer_right = '$count_answer_right',
    count_answer_false = '$count_answer_false',
    count_reject = '$count_reject',
    count_not_answer = '$count_not_answer',

    my_question_banned = '$my_question_banned',
    my_question_unverified = '$my_question_unverified',
    my_question_verified = '$my_question_verified',
    my_question_decided = '$my_question_decided',
    my_question_promoted = '$my_question_promoted',

    poin_membuat_soal = '$poin_membuat_soal',
    poin_tumbuh_soal = '$poin_tumbuh_soal',
    available_questions = '$available_questions',
    last_update = CURRENT_TIMESTAMP

    WHERE id=$id_peserta 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


  # ====================================================
  # GET RANK
  # ====================================================
  $s = "SELECT id,RANK() over (ORDER BY a.war_points DESC) rank FROM tb_perang_summary a";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while($d=mysqli_fetch_assoc($q)){
    if($d['id']==$id_peserta){
      $war_rank = $d['rank'];
      break;
    }
  }

  // reupdate rank
  $s = "UPDATE tb_perang_summary SET war_rank=$war_rank WHERE id=$id_peserta";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));  

  // zzz old system
  $s = "UPDATE tb_peserta SET last_update_available_soal=CURRENT_TIMESTAMP, available_soal=$available_questions WHERE id=$id_peserta 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
  $selisih_war = 1; // 1 detik yang lalu
  die('<script>location.reload()</script>');
}


if($available_soal>99) $available_soal = 99;
if($available_questions>99) $available_questions = 99;



# ============================================================
# VERIF COUNTS FOR DOSEN
# ============================================================
$jumlah_verif_war = 0;
$jumlah_verif_profil = 0;
if($id_role!=1){
  $rfiles = scandir('assets/img/peserta/');
  foreach ($rfiles as $file) {
    if(strpos("salt$file",'peserta-')){
      $jumlah_verif_profil++;
    }
    if(strpos("salt$file",'war-')){
      if(!strpos("salt$file",'reject.jpg')){
        $jumlah_verif_war++;
        // echo "<div style='margin-top: 300px; font-size: 50px'> $file</div>";
      }
    }
  }
}
