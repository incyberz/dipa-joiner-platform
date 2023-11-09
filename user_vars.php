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