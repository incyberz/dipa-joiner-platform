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
b.image as image_instruktur,
b.war_image as war_image_instruktur,
b.nama as nama_instruktur,
(
  SELECT id FROM tb_room_kelas 
  WHERE kelas='$kelas' 
  AND id_room='$id_room') id_room_kelas,
(
  SELECT count(1) FROM tb_room_kelas 
  WHERE id_room='$id_room'
  AND ta=$ta_aktif) total_kelas,
(
  SELECT last_update FROM tb_room_count  
  WHERE id_room='$id_room') last_update,
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
  die(div_alert('danger', "$Room tidak ditemukan. id_room: $id_room"));
}

$room = mysqli_fetch_assoc($q);
if ($room['jenjang'] == 'SD' || $room['jenjang'] == 'SP' || $room['jenjang'] == 'SA') {
  $senin_pertama_kuliah = $senin_pertama_sekolah;
  $ta_awal = $senin_pertama_sekolah;
  $ta_akhir = $akhir_sekolah;
}

# ============================================================
# STATUS ROOM VALIDATION
# ============================================================
$status_room = $room['status'];
$nama_instruktur = $room['nama_instruktur'];
if ($status_room != 100) {
  if ($parameter != 'aktivasi_room') {
    if ($id_role == 2) {
      echo div_alert('danger', "Status $Room tidak 100% ... Anda harus reactivate!");
      jsurl('?aktivasi_room', 2000);
    } else {
      die(div_alert('danger', "Status $Room ini belum siap untuk digunakan.<hr>Silahkan hubungi $Trainer [ $nama_instruktur ]  "));
    }
  }
}


# ============================================================
# TOTAL KELAS VALIDATION
# ============================================================
if ($id_role != 2) {
  if (!$room['total_kelas']) {
    die(div_alert('danger', "Belum ada satupun kelas pada $Room ini di TA= $ta_aktif"));
  }
}

# ============================================================
# SESI AKTIF ATAU SESI PERTAMA VALIDATION
# ============================================================
$s = "SELECT *,
(
  SELECT COUNT(1) FROM tb_sesi 
  WHERE jenis=1 
  AND id_room=$id_room 
  AND awal_presensi <= '$now'
  ) sesi_normal_count 
FROM tb_sesi WHERE id_room=$id_room AND awal_presensi < '$now' 
ORDER BY no DESC LIMIT 1
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$sesi_aktif = [];
$sesi_pertama = [];
if (!mysqli_num_rows($q)) {
  # ============================================================
  # COBA SESI PERTAMA JIKA TIDAK ADA SESI AKTIF
  # ============================================================
  $s = "SELECT * FROM tb_sesi WHERE no=1 AND id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $sesi_pertama = mysqli_fetch_assoc($q);
} else {
  // if (mysqli_num_rows($q) > 1) die(div_alert('danger', 'Terdapat multiple sesi dalam satu pekan.'));
  if (mysqli_num_rows($q) > 1) {
    echo (div_alert('danger', 'Terdapat multiple sesi dalam satu pekan.'));
    echo (div_alert('danger', 'Skipped by system.'));
  }
  $sesi_aktif = mysqli_fetch_assoc($q);
}
$id_sesi_aktif = null;
$no_sesi_aktif = null;
if (!(!$sesi_aktif and !$sesi_pertama)) {
  $id_sesi_aktif = $sesi_aktif ? $sesi_aktif['id'] : $sesi_pertama['id'];
  $no_sesi_aktif = $sesi_aktif ? $sesi_aktif['no'] : $sesi_pertama['no'];
}


# ============================================================
# EXTRACT ROOM DATA
# ============================================================
$singkatan_room = $room['singkatan'];
$nama_room = $room['nama'];
$id_room_kelas = $room['id_room_kelas'];
$id_instruktur = $room['id_instruktur'];
$jumlah_sesi = $room['count_sesi'];
$count_sesi = $room['count_sesi'];
if ($room['war_image_instruktur'] and file_exists("$lokasi_profil/$room[war_image_instruktur]")) {
  $path_profil_instruktur = "$lokasi_profil/$room[war_image_instruktur]";
} elseif ($room['image_instruktur'] and file_exists("$lokasi_profil/$room[image_instruktur]")) {
  $path_profil_instruktur = "$lokasi_profil/$room[image_instruktur]";
} else {
  $path_profil_instruktur = "$lokasi_profil/peserta-$room[id_instruktur].jpg"; // old code

}
$profil_instruktur = "<img src='$path_profil_instruktur' class='foto_profil' alt='profil_instruktur' />";

# ========================================================
# ASSIGN ROOM-KELAS VALIDATION | STOP JIKA KELAS BELUM DI-ASSIGN KE ROOM INI
# ========================================================
if (!$id_room_kelas) {

  if ($parameter != 'assign_room_kelas') {

    $assign_room_kelas = "<a href='?pilih_room'>Pilih $Room lainnya</a>";
    if ($id_role == 2) {
      if ($id_instruktur == $id_peserta) {
        $assign_room_kelas = "<a href='?manage_kelas'>Assign $Room Kelas</a>";
        $pesan = "Anda adalah pemilik $Room ini. Silahkan Assign kelas ini ke $Room Anda.";

        // auto-assign kelas sendiri ke $Room ini
        $s = "INSERT INTO tb_room_kelas (id_room,kelas,ta) VALUES ($id_room,'$kelas', $ta_aktif)";
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
        jsurl();
      } else {
        unset($_SESSION['dipa_id_room']);
        $pesan = "Silahkan hubungi beliau jika Kelas <u>$kelas</u> adalah benar anggota $Room tersebut.";
      }
    } else {
      unset($_SESSION['dipa_id_room']);
      echo "<hr>Unsetting selected $Room... success. Silahkan Anda pilih $Room lainnya!";
    }

    die(div_alert('danger', "
      <hr>
      <b style=color:red>Kelas <u>$kelas</u> belum di-assign ke $Room <u>$singkatan_room</u>.</b> 
      <hr>
      $Room ini dimiliki oleh:
      <ul>
        <li>Nama $Trainer: $nama_instruktur</li>
        <li>$pesan</li>
      </ul>
      <hr>
      $assign_room_kelas
    "));
  }
}


$select_all_from_tb_room_kelas = "SELECT * FROM tb_room_kelas WHERE id_room=$id_room AND kelas != 'INSTRUKTUR' AND ta=$ta_aktif";
























# ============================================================
# VALIDATIONS PASSED
# ============================================================


# ========================================================
# DATA INSTRUKTUR
# ========================================================
$s = "SELECT id,nama,folder_uploads,username,no_wa,gender,image,war_image FROM tb_peserta WHERE id=$room[created_by] AND id_role=2 AND status=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', "Data $Trainer untuk $Room ini tidak ditemukan (atau inactive)"));
$trainer = mysqli_fetch_assoc($q);

# =======================================================
# ROOM COUNT
# =======================================================
if (!$room['last_update']) {
  # ============================================================
  # CREATE ROOM COUNT
  # ============================================================
  $s = "INSERT INTO tb_room_count (id_room) VALUES ($id_room)";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
} elseif (
  $room['status'] == 100 and
  (
    ($id_role == 2 and (strtotime('now') - strtotime($room['last_update'])) > 600)
    ||
    (date('Y-m-d', strtotime($room['last_update'])) != $today)
  )
) {
  # ============================================================
  # UPDATE ROOM COUNT IF STATUS ROOM = 100
  # ============================================================
  if ($_POST) {
    // skip update  $Room count ketika ada post request
    $room_count = [];
    $room_count['sudah_uts'] = null;
    $room_count['sudah_uas'] = null;
  } else {
    include "$lokasi_pages/update_room_count.php";
    jsurl();
  }
} else {
  # ============================================================
  # ACCESS ROOM COUNT
  # ============================================================
  $s = "SELECT * FROM tb_room_count WHERE id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $room_count = mysqli_fetch_assoc($q);
  $total_peserta = $room_count['count_peserta'];
  $total_latihan = $room_count['count_latihan'];
  $total_challenge = $room_count['count_challenge'];
  $total_latihan_wajib = $room_count['count_latihan_wajib'];
  $total_challenge_wajib = $room_count['count_challenge_wajib'];
}

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
# MY POINT (TEMPORER)
# =========================================
$jeda_update_poin = 600; // boleh update setiap 10 menit
$s = "SELECT * FROM tb_poin WHERE id_room=$id_room AND id_peserta=$id_peserta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) > 1) die('Duplicate data poin in room_vars');
if (mysqli_num_rows($q)) {
  $my_poin = mysqli_fetch_assoc($q);

  $last_update_point = $my_poin['last_update_point'];
  $selisih = strtotime('now') - strtotime($last_update_point);

  if ($selisih >= $jeda_update_poin) $harus_update_poin = 1;

  $rank_room = $my_poin['rank_room'];
  $rank_kelas = $my_poin['rank_kelas'];

  $poin_bertanya = $my_poin['poin_bertanya'];
  $poin_menjawab = $my_poin['poin_menjawab'];
  $poin_latihan = $my_poin['poin_latihan'];
  $poin_challenge = $my_poin['poin_challenge'];
  $akumulasi_poin = $my_poin['akumulasi_poin'];

  $my_points = $akumulasi_poin;
  $my_poins_show = number_format($akumulasi_poin, 0);

  $nilai_akhir = $my_poin['nilai_akhir'];
  $nilai_akhir = $nilai_akhir > 100 ? 100 : $nilai_akhir;

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
  WHERE q.ta=$ta_aktif 
  AND r.id_role = $id_role 
  AND r.status = 1 
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





# ============================================================
# ROOM INFO UJIAN
# ============================================================
$info_uts = $room_count['sudah_uts'] ? 'Room ini Sudah UTS' : '<i class=abu>Belum UTS</i>';
$info_uas = $room_count['sudah_uas'] ? 'Room ini Sudah UAS' : '<i class=abu>Belum UAS</i>';
$room['info_ujian'] = "<div class='flexy f10 blue '><div>$info_uts</div><div>$info_uas</div></div>";
