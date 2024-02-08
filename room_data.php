<?php 
# ========================================================
# GET DATA POIN
# ========================================================
$s = "SELECT * FROM tb_poin WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  //auto-insert new data nilai
  $s = "INSERT INTO tb_poin (id_peserta,id_room) VALUES ($id_peserta,$id_room)";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  //re-get
  $s = "SELECT * FROM tb_poin WHERE id_peserta=$id_peserta AND id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}
$d_poin = mysqli_fetch_assoc($q);
$my_points = number_format($d_poin['akumulasi_poin'],0);



# ========================================================
# GET DATA PROSES BELAJAR
# ========================================================
$s = "SELECT 
(SELECT 1 FROM tb_polling_answer WHERE id_room=$id_room AND id_untuk=concat(a.id,'-uts')) sudah_polling_uts,
(SELECT 1 FROM tb_polling_answer WHERE id_room=$id_room AND id_untuk=concat(a.id,'-uas')) sudah_polling_uas,
(
  SELECT count(1) FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  JOIN tb_peserta r ON p.id_peserta=r.id 
  WHERE q.tahun_ajar=$tahun_ajar 
  AND r.id_role = $id_role 
  AND r.status = 1 
  AND q.kelas='$kelas') total_peserta_kelas 

FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.username='$username'";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("mhs_var :: Mahasiswa dengan Username: $username tidak ditemukan.");
$d = mysqli_fetch_assoc($q);
$sudah_polling_uts = $d['sudah_polling_uts'];
$sudah_polling_uas = $d['sudah_polling_uas'];
$total_peserta_kelas = $d['total_peserta_kelas'];






# =======================================================
# COUNT TOTAL LATIHAN / TUGAS / CHALLENGE
# =======================================================
$s = "SELECT 
(
  SELECT count(1) FROM tb_kelas_peserta p 
  JOIN tb_room_kelas q ON p.kelas=q.kelas 
  JOIN tb_kelas r ON p.kelas=r.kelas 
  WHERE q.id_room=$id_room 
  AND r.status=1 
  ) total_peserta,
(SELECT COUNT(1) FROM tb_latihan WHERE status=1 AND id_room=$id_room) total_latihan,
(SELECT COUNT(1) FROM tb_challenge WHERE status=1 AND id_room=$id_room) total_challenge
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$total_peserta = $d['total_peserta'];
$total_latihan = $d['total_latihan'];
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
$s = "SELECT a.id_peserta 
FROM tb_poin a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas_peserta c ON b.id=c.id_peserta 
JOIN tb_kelas d ON c.kelas=d.kelas 
WHERE a.id_room=$id_room 
AND b.status = 1 
AND b.id_role = 1 
AND d.tahun_ajar = $tahun_ajar 
AND d.status = 1 
AND c.kelas = '$kelas'

ORDER BY a.akumulasi_poin DESC;
";
// echo $s;
// die("<pre>$s</pre>");
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$rank_kelas = 1;
$i=1;
while($d=mysqli_fetch_assoc($q)){
  if($d['id_peserta']==$id_peserta){
    $rank_kelas = $i;
    break;
  }
  $i++;
}
/*
if(!$rank_kelas){
  $s = "SELECT a.id,  
  -- RANK() over (ORDER BY a.akumulasi_poin DESC) as rank 
  (SELECT 1) rank 
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
*/

// $rank_kelas = 1;

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
$s = "SELECT AVG(a.akumulasi_poin) as average 
FROM tb_poin a 
JOIN tb_peserta b ON a.id_peserta=b.id 
WHERE b.status=1 
AND a.id_room=$id_room 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$average = $d['average'] ?? 1;
$nilai_akhir = $d_poin['nilai_akhir'] ?? 0;
// $nilai_akhir = !$nilai_akhir ? round(($d_poin['akumulasi_poin'] / ($average*1.2)) *100,0) : $nilai_akhir;
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
