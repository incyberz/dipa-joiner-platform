<?php
if (!$id_room) die(erid('id_room'));
if (!$kelas) {
  die("
  <b style=color:red>
  Username Anda belum dimasukan ke kelas manapun atau tidak dijadikan sebagai Grup INSTRUKTUR</b>
  <hr>
  Silahkan hubungi Developer (Iin, M.Kom) untuk Verifikasi akun Anda!
  <hr>
  <a href='?logout'>Logout</a>
  ");
}

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
  WHERE id_room='$id_room') last_update_room,
(
  SELECT COUNT(1) FROM tb_sesi  
  WHERE id_room='$id_room') count_sesi


FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id 
WHERE a.id=$id_room 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

if (!mysqli_num_rows($q)) {
  unset($_SESSION['dipa_id_room']);
  die(div_alert('danger', "Room tidak ditemukan. id_room: $id_room"));
}

$room = mysqli_fetch_assoc($q);
$status_room = $room['status'];
$nama_instruktur = $room['nama_instruktur'];
if ($status_room != 100) {
  if ($parameter != 'aktivasi_room') {
    if ($id_role == 2) {
      echo div_alert('danger', "Status Room tidak 100% ... Anda harus reactivate!");
      jsurl('?aktivasi_room', 2000);
    } else {
      die(div_alert('danger', "Statu Room ini belum siap untuk digunakan.<hr>Silahkan hubungi Instruktur [ $nama_instruktur ]  "));
    }
  }
}
$singkatan_room = $room['singkatan'];
$nama_room = $room['nama'];
$id_room_kelas = $room['id_room_kelas'];
$id_instruktur = $room['id_instruktur'];
$last_update_room = $room['last_update_room'];
$jumlah_sesi = $room['count_sesi'];
$count_sesi = $room['count_sesi'];

# ========================================================
# PROFILE INSTRUKTUR
# ========================================================
$path_profil_instruktur = "$lokasi_profil/peserta-$id_instruktur.jpg";
$profil_instruktur = "<img src='$path_profil_instruktur' class='foto_profil' alt='profil_instruktur' />";

# ========================================================
# STOP JIKA KELAS BELUM DI-ASSIGN KE ROOM INI
# ========================================================
if (!$id_room_kelas) {

  if ($parameter != 'assign_room_kelas') {

    $assign_room_kelas = '<a href="?pilih_room">Pilih Room lainnya</a>';
    if ($id_role == 2) {
      if ($id_instruktur == $id_peserta) {
        $assign_room_kelas = "<a href='?assign_room_kelas'>Assign Room Kelas</a>";
        $pesan = "Anda adalah pemilik room ini. Silahkan Assign kelas ini ke Room Anda.";

        // auto-assign kelas sendiri ke room ini
        $s = "INSERT INTO tb_room_kelas (id_room,kelas) VALUES ($id_room,'$kelas')";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        jsurl();
      } else {
        unset($_SESSION['dipa_id_room']);
        $pesan = "Silahkan hubungi beliau jika Kelas <u>$kelas</u> adalah benar anggota room tersebut.";
      }
    } else {
      unset($_SESSION['dipa_id_room']);
      echo '<hr>Unsetting selected room... success. Silahkan Anda pilih room lainnya!';
    }

    die(div_alert('danger', "
      <hr>
      <b style=color:red>Kelas <u>$kelas</u> belum di-assign ke room <u>$singkatan_room</u>.</b> 
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
  SELECT COUNT(1) 
  FROM tb_kelas_peserta p 
  JOIN tb_kelas q ON p.kelas=q.kelas 
  JOIN tb_room_kelas r ON q.kelas=r.kelas 
  JOIN tb_peserta s ON p.id_peserta=s.id  
  WHERE r.id_room=$id_room 
  AND q.status=1 -- hanya kelas aktif
  AND s.status=1 -- hanya peserta aktif
  AND s.id_role=1 -- hanya peserta
  AND s.nama NOT LIKE '%dummy%' 
  ) total_peserta,

(
  SELECT COUNT(1) 
  FROM tb_assign_latihan 
  WHERE id_room_kelas=$id_room_kelas) total_latihan,
(
  SELECT COUNT(1) 
  FROM tb_assign_latihan 
  WHERE id_room_kelas=$id_room_kelas 
  AND is_wajib is not null) total_latihan_wajib,

(
  SELECT COUNT(1) 
  FROM tb_assign_challenge 
  WHERE id_room_kelas=$id_room_kelas) total_challenge,
(
  SELECT COUNT(1) 
  FROM tb_assign_challenge 
  WHERE id_room_kelas=$id_room_kelas 
  AND is_wajib is not null) total_challenge_wajib

";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$total_peserta = $d['total_peserta'];
$total_latihan = $d['total_latihan'];
$total_challenge = $d['total_challenge'];
$total_latihan_wajib = $d['total_latihan_wajib'];
$total_challenge_wajib = $d['total_challenge_wajib'];


# ============================================================
# VERIF COUNTS FOR INSTRUKTUR
# ============================================================
$jumlah_verif_war = 0;
$jumlah_verif_profil = 0;
if ($id_role == 2) {
  $rfiles = scandir($lokasi_profil);
  foreach ($rfiles as $file) {
    if (strpos("salt$file", 'peserta-')) {
      $jumlah_verif_profil++;
    }
    if (strpos("salt$file", 'war-')) {
      if (!strpos("salt$file", 'reject.jpg')) {
        $jumlah_verif_war++;
      }
    }
  }
}








































# ========================================================
# MY ROOM VARS
# ========================================================
if (!$id_peserta) die(erid('$id_peserta'));

# =========================================
# MY DATA POIN TEMPORER
# =========================================
$jeda_update_poin = 600; // boleh update setiap 10 menit
$s = "SELECT * FROM tb_poin WHERE id_room=$id_room AND id_peserta=$id_peserta";
// echo "<hr>ZZZ $s";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) > 1) die('Duplicate data poin in room_vars');
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  // echo 'ZZZ<pre>';
  // var_dump($d);
  // echo '</pre>';
  $last_update_point = $d['last_update_point'];
  $selisih = strtotime('now') - strtotime($last_update_point);
  // echo "<hr>ZZZ $selisih<hr>";
  // exit;
  if ($selisih >= $jeda_update_poin) $harus_update_poin = 1;
  // $harus_update_poin = 1; // ZZZ

  $rank_room = $d['rank_room'];
  $rank_kelas = $d['rank_kelas'];
  // echo "<hr>ZZZ $rank_kelas";
  // exit;
  $poin_bertanya = $d['poin_bertanya'];
  $poin_menjawab = $d['poin_menjawab'];
  $poin_latihan = $d['poin_latihan'];
  $poin_challenge = $d['poin_challenge'];
  $akumulasi_poin = $d['akumulasi_poin'];

  $my_points = $akumulasi_poin;
  $my_points_show = number_format($akumulasi_poin, 0);

  $nilai_akhir = $d['nilai_akhir'];
  $nilai_akhir = $nilai_akhir > 100 ? 100 : $nilai_akhir;

  if ($nilai_akhir > 100) die('Invalid nilai akhir at room_vars');
  $hm = $nilai_akhir ? hm($nilai_akhir) : 'B';

  $th = $rank_kelas ? th($rank_kelas) : '?';
  $th_global = $rank_room ? th($rank_room) : '?';
} else { // belum punya data poin
  //auto create data poin
  $s = "INSERT INTO tb_poin (id_room,id_peserta,last_update_point) VALUES ($id_room,$id_peserta,'2024-1-1')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
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
  WHERE q.tahun_ajar=$ta 
  AND r.id_role = $id_role 
  AND r.status = 1 
  AND r.nama NOT LIKE '%dummy%' 
  AND q.kelas='$kelas') total_peserta_kelas 

FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.id='$id_peserta'";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die("mhs_var :: Mahasiswa dengan id: $id_peserta tidak ditemukan.");
$d = mysqli_fetch_assoc($q);
$sudah_polling_uts = $d['sudah_polling_uts'];
$sudah_polling_uas = $d['sudah_polling_uas'];
$total_peserta_kelas = $d['total_peserta_kelas'];
