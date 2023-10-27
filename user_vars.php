<?php 
$today=date('Y-m-d');
$undef = '<span class="red kecil miring">undefined</span>';

# ========================================================
# GET DATA PESERTA
# ========================================================
$s = "SELECT a.*, b.sebagai,
(SELECT 1 FROM tb_biodata WHERE id_peserta=a.id) punya_biodata 
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

// $img_profile = "uploads/$folder_uploads/img_profile_$username.jpg";

// $img_bg = "uploads/$folder_uploads/img_bg_$username.jpg";
// if(!file_exists($img_bg)) $img_bg = "uploads/bg_na.jpg";

$path_profile = "assets/img/peserta/peserta-$id_peserta.jpg";
$rand = rand(1,5);
$path_profile_na = "assets/img/no_profile$rand.jpg";
if(file_exists($path_profile)){
  $punya_profil = true;
}else{
  $punya_profil = false;
  $path_profile = $path_profile_na;
}


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




# ========================================================
# RANK DAN JUMLAH PESERTA
# ========================================================
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

if($ranks[$id_peserta]){
  $rank = $ranks[$id_peserta];
  if($rank%10==1){
    $th = 'st';
  }elseif($rank%10==2){
    $th = 'nd';
  }elseif($rank%10==3){
    $th = 'rd';
  }else{
    $th = 'th';
  }
}else{
  $rank = '?';
  $th = '';
}

# ============================================================
# AVERAGE DAN NILAI AKHIR
# ============================================================
$s = "SELECT AVG(akumulasi_poin) as average FROM tb_peserta WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$average = $d['average'];
$nilai_akhir = round(($akumulasi_poin / ($average*1.2)) *100,0);
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
echo "<section><pre>$average | $nilai_akhir</pre></section>";









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



