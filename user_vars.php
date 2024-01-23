<?php 
if(!$tahun_ajar) die(erid('tahun_ajar at user_vars'));
if($dm) echo "<div style='height:50px'>.</div>DEBUG MODE ON<hr>";  

$today=date('Y-m-d');
$undef = '<span class="red kecil miring">undefined</span>';

# ========================================================
# SELECT DATA PESERTA
# ========================================================
$s = "SELECT a.id as id_peserta, a.* 
FROM tb_peserta a WHERE a.username='$username' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)) die('Username tidak ditemukan.');
$d=mysqli_fetch_assoc($q);
$id_peserta = $d['id_peserta'];
$nama_peserta = ucwords(strtolower($d['nama']));
$no_wa = $d['no_wa'] ?? '';
$no_wa_show = $no_wa==''?$undef:substr($no_wa,0,4).'***'.substr($no_wa,strlen($no_wa)-3,3);
$password = $d['password'];
$is_depas = $password=='' ? 1 : 0;
$status = $d['status'];
$profil_ok = $d['profil_ok'];
$last_update_available_soal = $d['last_update_available_soal'];
$available_soal = $d['available_soal'];


# ========================================================
# FOLDER UPLOADS HANDLER
# ========================================================
$folder_uploads = $d['folder_uploads'];
$id_role = $d['id_role'];
if($folder_uploads==''){
  # ========================================================
  # AUTO-CREATE FOLDER UPLOADS
  # ========================================================
  $a = '_'.strtolower($d['nama']);
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



# ========================================================
# GET DATA POIN
# ========================================================
$s = "SELECT * FROM tb_poin WHERE id_peserta=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$akumulasi_poin = $d['akumulasi_poin'] ?? 0;
$my_points = number_format($akumulasi_poin,0);
$uts = $d['uts'] ?? 0;
$uas = $d['uas'] ?? 0;
$nilai_akhir = $d['nilai_akhir'] ?? 0;
$rank_kelas = $d['rank_kelas'] ?? 0;
$rank_global = $d['rank_global'] ?? 0;
$poin_latihan = $d['poin_latihan'] ?? 0;
$poin_tugas = $d['poin_tugas'] ?? 0;
$poin_challenge = $d['poin_challenge'] ?? 0;
$poin_bertanya = $d['poin_bertanya'] ?? 0;
$poin_menjawab = $d['poin_menjawab'] ?? 0;


# ========================================================
# GET DATA PROSES BELAJAR
# ========================================================
$s = "SELECT b.sebagai,
(SELECT 1 FROM tb_biodata WHERE id=a.id) punya_biodata,
(SELECT nik FROM tb_biodata WHERE id=a.id) nik,
(SELECT 1 FROM tb_polling_answer WHERE id_untuk=concat(a.id,'-uts')) sudah_polling_uts,
(SELECT 1 FROM tb_polling_answer WHERE id_untuk=concat(a.id,'-uas')) sudah_polling_uas,
(
  SELECT p.kelas FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE q.tahun_ajar=$tahun_ajar
  AND p.id_peserta=$id_peserta) kelas,
(
  SELECT count(1) FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE q.tahun_ajar=$tahun_ajar) total_kelas_ini 

FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.username='$username'";
echo $s;
$sd = $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("mhs_var :: Mahasiswa dengan Username: $username tidak ditemukan.");
$d_peserta = mysqli_fetch_assoc($q);


$punya_biodata = $d_peserta['punya_biodata'];
$nik = $d_peserta['nik'];
$sudah_polling_uts = $d_peserta['sudah_polling_uts'];
$sudah_polling_uas = $d_peserta['sudah_polling_uas'];

$total_kelas_ini = $d_peserta['total_kelas_ini'];

$total_kelas_peserta = $total_kelas_ini;


$kelas = $d_peserta['kelas'];
$sebagai = $d_peserta['sebagai'];



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
$s = "SELECT AVG(a.akumulasi_poin) as average FROM tb_poin a 
JOIN tb_peserta b ON a.id_peserta=b.id WHERE b.status=1";
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









// $pekerjaan = '';
// $testimony = '';
// $alamat = '';
// $desa = '';
// $kec = '';
// $kab = '';
// if($punya_biodata){
//   $s = "SELECT * FROM tb_biodata WHERE id_peserta=$id_peserta";
//   $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
//   if(mysqli_num_rows($q)){
//     $d=mysqli_fetch_assoc($q);
//     $pekerjaan = $d['pekerjaan'];
//     $testimony = $d['testimony'];
//     $alamat = $d['alamat'];
//     $desa = $d['desa'];
//     $kec = $d['kec'];
//     $kab = $d['kab'];
//   }
// }







# ============================================================
# AUTO-SELF UPDATE EVERY HOUR | AVAILABLE SOAL
# ============================================================
$selisih = $id_role==1 ? (strtotime('now') - strtotime($last_update_available_soal)) : 0;
if($selisih>3600 || $available_soal==''){
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


if($dm)
echo "
  <div class='debug'>
    <h1>Debugging User Vars</h1>
    <br>kelas: $kelas
  </div>
";
