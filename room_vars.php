<?php 
if(!$id_room) die(erid('id_room'));
if(!$kelas) die(erid('$kelas'));

# ========================================================
# GET DATA ROOM
# ========================================================
$s = "SELECT a.*, 
b.id as id_instruktur,
b.nama as nama_instruktur,
(
  SELECT id FROM tb_room_kelas 
  WHERE kelas='$kelas' 
  AND id_room='$id_room') id_room_kelas,
(
  SELECT last_update FROM tb_room_stats  
  WHERE id_room='$id_room') last_update_room


FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id 
WHERE a.id=$id_room 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

if(!mysqli_num_rows($q)){
  unset($_SESSION['dipa_id_room']);
  die(div_alert('danger',"Room tidak ditemukan. id_room: $id_room"));
} 

$d = mysqli_fetch_assoc($q);
$room = $d['singkatan'];
$nama_room = $d['nama'];
$status_room = $d['status'];
$id_room_kelas = $d['id_room_kelas'];
$id_instruktur = $d['id_instruktur'];
$nama_instruktur = $d['nama_instruktur'];
$last_update_room = $d['last_update_room'];

# ========================================================
# PROFILE INSTRUKTUR
# ========================================================
$path_profil_instruktur = "assets/img/peserta/peserta-$id_instruktur.jpg";
$profil_instruktur = "<img src='$path_profil_instruktur' class='foto_profil' alt='profil_instruktur' />";

# ========================================================
# STOP JIKA KELAS BELUM DI-ASSIGN KE ROOM INI
# ========================================================
if(!$id_room_kelas){
  
  if($parameter!='assign_room_kelas'){
    
    $assign_room_kelas = '<a href="?pilih_room">Pilih Room lainnya</a>';
    if($id_role==2){
      if($id_instruktur==$id_peserta){
        $assign_room_kelas = "<a href='?assign_room_kelas'>Assign Room Kelas</a>";
        $pesan = "Anda adalah pemilik room ini. Silahkan Assign kelas ini ke Room Anda.";
      }else{
        unset($_SESSION['dipa_id_room']);
        $pesan = "Silahkan hubungi beliau jika Kelas <u>$kelas</u> adalah benar anggota room tersebut.";
      }
    }else{
      unset($_SESSION['dipa_id_room']);
      echo '<hr>Unsetting selected room... success. Silahkan Anda pilih room lainnya!';
    }

    die(div_alert('danger',"
      <hr>
      <b style=color:red>Kelas <u>$kelas</u> belum di-assign ke room <u>$room</u>.</b> 
      <hr>
      Room ini dimiliki oleh:
      <ul>
        <li>Nama Instruktur: $nama_instruktur</li>
        <li>$pesan</li>
      </ul>
      <hr>
      $assign_room_kelas
    "));
  }
}



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
(SELECT COUNT(1) FROM tb_latihan WHERE id_room=$id_room) total_latihan,
(SELECT COUNT(1) FROM tb_challenge WHERE id_room=$id_room) total_challenge
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$total_peserta = $d['total_peserta'];
$total_latihan = $d['total_latihan'];
$total_challenge = $d['total_challenge'];


# ============================================================
# VERIF COUNTS FOR DOSEN
# ============================================================
$jumlah_verif_war = 0;
$jumlah_verif_profil = 0;
if($id_role==2){
  $rfiles = scandir('assets/img/peserta/');
  foreach ($rfiles as $file) {
    if(strpos("salt$file",'peserta-')){
      $jumlah_verif_profil++;
    }
    if(strpos("salt$file",'war-')){
      if(!strpos("salt$file",'reject.jpg')){
        $jumlah_verif_war++;
      }
    }
  }
}








































# ========================================================
# MY ROOM VARS
# ========================================================
if(!$id_peserta) die(erid('$id_peserta'));

# =========================================
# MY DATA POIN TEMPORER
# =========================================
$harus_update_poin = 0;
$jeda_update_poin = 600; // boleh update setiap 10 menit
$s = "SELECT * FROM tb_poin WHERE id_room=$id_room AND id_peserta=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)>1) die('Duplicate data poin in room_vars');
if(mysqli_num_rows($q)){
  $d = mysqli_fetch_assoc($q);
  $last_update_point = $d['last_update_point'];
  $selisih = strtotime('now') - strtotime($last_update_point);
  if($selisih>=$jeda_update_poin) $harus_update_poin=1; 
  
  $rank_global = $d['rank_global'];
  $rank_kelas = $d['rank_kelas'];
  $poin_bertanya = $d['poin_bertanya'];
  $poin_menjawab = $d['poin_menjawab'];
  $poin_latihan = $d['poin_latihan'];
  $poin_challenge = $d['poin_challenge'];
  $akumulasi_poin = $d['akumulasi_poin'];
  
  $my_points = number_format($akumulasi_poin,0);

  $nilai_akhir = $d['nilai_akhir'];
  if($nilai_akhir>100) die('Invalid nilai akhir at room_vars');
  $hm = $nilai_akhir ? hm($nilai_akhir) : 'B';

  $th = $rank_kelas ? th($rank_kelas) : '?';
  $th_global = $rank_global ? th($rank_global) : '?';


}else{ // belum punya data poin
  //auto create data poin
  $s = "INSERT INTO tb_poin (id_room,id_peserta,last_update_point) VALUES ($id_room,$id_peserta,'2024-1-1')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();
}





# ========================================================
# MY POLLING
# ========================================================
$s = "SELECT 
(
  SELECT 1 FROM tb_polling_answer 
  WHERE id_room=$id_room 
  AND id_untuk=concat(a.id,'-uts')) sudah_polling_uts,
(
  SELECT 1 FROM tb_polling_answer 
  WHERE id_room=$id_room 
  AND id_untuk=concat(a.id,'-uas')) sudah_polling_uas,
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
WHERE a.id='$id_peserta'";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die("mhs_var :: Mahasiswa dengan id: $id_peserta tidak ditemukan.");
$d = mysqli_fetch_assoc($q);
$sudah_polling_uts = $d['sudah_polling_uts'];
$sudah_polling_uas = $d['sudah_polling_uas'];
$total_peserta_kelas = $d['total_peserta_kelas'];














































if($harus_update_poin and $id_room_kelas){
  echo '<div class="consolas f12 abu">Updating Points... please wait!<hr>';
  # ========================================================
  # HITUNG MY RANK KELAS
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
  echo "<br>updating rank_kelas... rank: $rank_kelas";
  
  
  
  # ========================================================
  # HITUNG MY RANK GLOBAL
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
  
  ORDER BY a.akumulasi_poin DESC;
  ";
  // echo $s;
  // die("<pre>$s</pre>");
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $rank_global = 1;
  $i=1;
  while($d=mysqli_fetch_assoc($q)){
    if($d['id_peserta']==$id_peserta){
      $rank_global = $i;
      break;
    }
    $i++;
  }
  echo "<br>updating rank_global... rank: $rank_global";
  
  # ========================================================
  # HITUNG MY POIN PRESENSI
  # HITUNG MY POIN BERTANYA
  # HITUNG MY POIN MENJAWAB
  # HITUNG MY POIN LATIHAN
  # HITUNG MY POIN CHALLENGE 
  # HITUNG MY POIN PLAY KUIS
  # HITUNG MY POIN TANAM SOAL 
  # HITUNG MY AKUMULASI POIN
  # ========================================================
  $s = "SELECT 
    (
      SELECT poin_presensi 
      FROM tb_presensi_summary   
      WHERE id_peserta=a.id 
      AND id_room=$id_room ) poin_presensi,
    (
      SELECT SUM(poin) 
      FROM tb_pertanyaan  
      WHERE id_penanya=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null) poin_bertanya,
    (
      SELECT SUM(poin) 
      FROM tb_pertanyaan_reply  
      WHERE id_penjawab=a.id 
      AND id_room_kelas = $id_room_kelas  
      AND verif_date is not null) poin_menjawab,
    (
      SELECT SUM(p.get_point) 
      FROM tb_bukti_latihan p 
      JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND status=1
      AND p.tanggal_verifikasi is not null) poin_latihan, 
    (
      SELECT SUM(p.get_point) 
      FROM tb_bukti_challenge p 
      JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id 
      WHERE p.id_peserta=a.id 
      AND q.id_room_kelas = $id_room_kelas  
      AND status=1 
      AND p.tanggal_verifikasi is not null ) poin_challenge, 
    (
      SELECT (war_point_quiz + war_point_reject) FROM tb_war_summary   
      WHERE id_peserta=a.id 
      AND id_room = $id_room) poin_play_kuis,
    (
      SELECT war_point_passive FROM tb_war_summary   
      WHERE id_peserta=a.id 
      AND id_room = $id_room) poin_tanam_soal 

  FROM tb_peserta a 
  WHERE a.id=$id_peserta 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  
  $akumulasi_poin = 0;
  foreach ($d as $key => $poin) {
    $akumulasi_poin += $poin;
    echo "<br>updating $key... poin: $poin";
  }
  
  
  # ========================================================
  # RE-UPDATE MY POINTS
  # ========================================================
  $s = "UPDATE tb_poin SET 

  rank_kelas = $rank_kelas,
  rank_global = $rank_global,
  poin_presensi = '$d[poin_presensi]',
  poin_bertanya = '$d[poin_bertanya]',
  poin_menjawab = '$d[poin_menjawab]',
  poin_latihan = '$d[poin_latihan]',
  poin_challenge = '$d[poin_challenge]',
  poin_play_kuis = '$d[poin_play_kuis]',
  poin_tanam_soal = '$d[poin_tanam_soal]',
  akumulasi_poin = $akumulasi_poin,

  last_update_point = CURRENT_TIMESTAMP 

  WHERE id_room=$id_room
  AND id_peserta=$id_peserta
  ";
  echo "<br>updating poin data... ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo 'success.';
  
  jsurl();
}