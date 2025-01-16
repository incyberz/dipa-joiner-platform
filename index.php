<?php
# ========================================================
# DIPA INDEX
# ========================================================
// if (0) {
//   die("
//   <style>*{margin:0;padding:0;background:black;color:white;text-align:center}
//   hr{margin:15px}</style>
//   <div style='padding:15px'>
//     <h1 style='color:yellow'>DIPA Joiner sedang maintenance.</h1>
//     <hr>
//     <p>Mohon maaf, saat ini sedang update Fitur Ujian dan Polling System</p>
//     <hr>
//     <p>Jika maintenance melebihi pukul 11.00 harap hubungi Pihak Developer. Terimakasih.</p>
//   </div>");
// }
session_start();
// session_destroy();

# ============================================================
# DEFAULT GLOBAL VARIABLE 
# ============================================================
$is_custom = false;
$meta_title = "DIPA Joiner Gamified LMS - Fun Learning Management System bagi Mitra, Praktisi, dan Akademisi";
$meta_description = "Fun e-Learning Management System (LMS) berbasis Game Mechanics (Gamification) bagi Mitra (Dunia Industri), Praktisi, dan Akademisi. Dengan Rank System, Leaderboard, Play Quiz, dan Tanam Soal, menjadikan Pembelajaran seindah permainan.";
$meta_keywords = "learning management system, fun lms, gamification, game mechanic, rank, leaderboard, quiz, bank soal, pembelajaran jarak jauh";

$Institusi = 'Firdaus Consultant';
$Nama_LMS = 'DIPA Joiner System';
$Room = 'Room';
$Trainer = 'Trainer';
$Peserta = 'Peserta';
$Praktisi = 'Praktisi';
$Mitra = 'Mitra';
$Join = 'Join';
$Slogan = 'Memadukan Dunia Industri, Praktisi, dan Akademisi dalam kebaikan';
$Leaderboard = 'Leaderboard';

$ops = [
  'nama' => 'Iin Sholihin',
  'username' => 'abi',
  'whatsapp' => '6287729007318',
  'email' => 'isholihin87@gmail.com',
];

$awal_nilai['A'] = 80;
$awal_nilai['B'] = 70;
$awal_nilai['C'] = 60;
$awal_nilai['D'] = 50;
$awal_nilai['E'] = 1;



# ============================================================
# DEFAULT VS CUSTOM GLOBAL VARIABLE 
# ============================================================
$path_custom = 'custom';
$file_custom = "$path_custom/custom.php";
if (file_exists($file_custom)) include $file_custom;



# ============================================================
# GLOBAL VARIABLE
# ============================================================
$dm = 0;
$id_role = null;
$status = null;
$punya_profil = null;
$available_questions = 0;
$kelas = null;
$sebagai = null;
$my_points = null;
$total_peserta = null;
$total_peserta_kelas = null;
$profil_ok = null;

$id_room_kelas = null;
$singkatan_room = null;
$nama_room = null;

$target_kelas = $_SESSION['target_kelas'] ?? null;
$harus_update_poin = 0;

$unset = '<span class="consolas f12 red miring">unset</span>';
$null_red = '<span class="consolas f12 red miring">null</span>';
$null = '<span class="f12 miring small">--null--</span>';

$lokasi_pages = 'pages';
$lokasi_profil = 'assets/img/peserta';
$lokasi_img = 'assets/img';
$src_profil_na_fixed = 'assets/img/img_na.jpg';

$is_login_as = isset($_SESSION['dipa_master_username']) ? 1 : 0;




# ============================================================
# DATABASE CONNECTION
# ============================================================
include 'conn.php';

# ============================================================
# TAHUN AJAR AKTIF
# ============================================================
$ta_aktif = 20241;
include 'config_ta.php';

# ========================================================
# COOKIE AND LOGIN PROCESS
# ========================================================
$dipa_cookie = 'dipa_username';



# ========================================================
# INCLUDE LOGIN PETUGAS
# ========================================================
$id_peserta = '';
$nama_peserta = '';
$is_login = 0;
if (isset($_SESSION['dipa_username'])) {
  $username = $_SESSION['dipa_username'];
  include 'user_vars.php';
  $is_login = 1;
} else {
  $username = '';
}


# ========================================================
# MANAGE URI
# ========================================================
$a = $_SERVER['REQUEST_URI'];
if (!strpos($a, "?")) $a .= "?";
if (!strpos($a, "&")) $a .= "&";

$b = explode("?", $a);
$c = explode("&", $b[1]);
$parameter = $c[0];
if ($parameter == 'logout') {
  include 'pages/logout.php';
  exit;
}

# ========================================================
# INCLUDES PURE PHP
# ========================================================
$arr_includes = [
  'insho_functions',
  'dipa_functions',
  'fungsi_alert',
  'fungsi_session_login',
  'date_managements',
  'href_wa',
  'get_current_url',
  // 'erid',
  'redirect',
  'echolog',
  'alert',
];
foreach ($arr_includes as $v) {
  $file = "includes/$v.php";
  if (file_exists($file)) {
    include $file;
  } elseif (file_exists("../$file")) {
    include "../$file"; // at htdocs or main server

  } else {
    die("<b style=color:red>File include [ $v ] diperlukan untuk menjalankan sistem.</b>");
  }
}

// include 'includes/insho_functions.php';
// include 'includes/dipa_functions.php';
// include 'includes/fungsi_alert.php';
// include 'includes/fungsi_session_login.php';
// include 'includes/date_managements.php';
// include 'includes/href_wa.php';
// include 'includes/get_current_url.php';
$ta_show = tahun_ajar_show($ta);


# ========================================================
# ROOM PROPERTIES
# ========================================================
# null = 0 = belum aktif
# -1 = selesai (auto jika tidak diantara awal dan akhir )
# 1 = aktif
# ========================================================
$id_room = $_SESSION['dipa_id_room'] ?? '';
$status_room = '';
if ($username) {
  if ($id_room) {
    include 'room_vars.php';
    // include 'room_data.php';
    include 'wars_data.php';
  } else { // belum pilih room
    if ($password) { // jika password OK
      if ($parameter != 'buat_room' and $parameter != 'verifikasi_wa') {
        // wajib pilih room dahulu        
        $parameter = 'pilih_room';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?= $meta_title ?></title>
  <meta content="<?= $meta_description ?>" name="description">
  <meta content="<?= $meta_keywords ?>" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> -->

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="assets/js/jquery.min.js"></script>
  <?php
  # ============================================================
  # INCLUDE STYLES
  # ============================================================
  include 'dipa_styles.php';
  $insho_styles = $is_live ? 'includes/insho_styles.php' : '../includes/insho_styles.php';
  include $insho_styles;
  include 'includes/meme.php';
  include 'includes/img_icon.php';
  ?>
</head>

<body>
  <div class="hideit" id="ta"><?= $ta ?></div>
  <?php

  if (!$is_login || $id_room) include 'pages/header.php';
  if (!$is_login and $parameter == '') {
    include $is_custom ? "$path_custom/custom-hero.php" : 'pages/hero.php';
  }
  ?>
  <main id="main">
    <section>
      <div class="container">
        <?php include 'routing.php'; ?>
      </div>
    </section>
  </main>
  <?php include 'update_points.php'; ?>
  <?php include 'pages/footer.php'; ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <!-- <script src="assets/vendor/php-email-form/validate.js"></script> -->

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>

<?php include 'includes/js_btn_aksi.php'; ?>