<?php
# ========================================================
# AKADEMIK INDEX
# ========================================================
if(0){
  die("
  <style>*{margin:0;padding:0;background:black;color:white;text-align:center}
  hr{margin:15px}</style>
  <div style='padding:15px'>
    <h1 style='color:yellow'>DIPA Joiner sedang maintenance.</h1>
    <hr>
    <p>Mohon maaf, saat ini sedang restrukturisasi database dan penambahan fitur Kelas Peserta untuk tiap ranks, grades, dan points</p>
    <hr>
    <p>Jika maintenance melebihi pukul 10.00 harap hubungi Pihak Developer. Terimakasih.</p>
  </div>");
}
session_start();
// session_destroy(); exit;
// echo '<pre style="margin-top: 170px">'; var_dump($_SESSION); echo '</pre>';
$dm = 1;
$is_login = null;
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

$tahun_ajar = 20232;
$id_room_kelas = null;
$room = null;
$nama_room = null;

$target_kelas = $_SESSION['target_kelas'] ?? null;

$unset = '<span class="consolas f12 red miring">unset</span>';
$null = '<span class="consolas f12 red miring">null</span>';

include 'config.php';

# ========================================================
# COOKIE AND LOGIN PROCESS
# ========================================================
$dipa_cookie = 'dipa_username';



# ========================================================
# INCLUDE LOGIN PETUGAS
# ========================================================
$id_peserta = '';
$nama_peserta = '';
if(isset($_SESSION['dipa_username'])){
  $username = $_SESSION['dipa_username'];
  include 'user_vars.php';
  $is_login=1;
}else{
  $username = '';
}


# ========================================================
# MANAGE URI
# ========================================================
$a = $_SERVER['REQUEST_URI'];
if (!strpos($a, "?")) $a.="?";
if (!strpos($a, "&")) $a.="&";

$b = explode("?", $a);
$c = explode("&", $b[1]);
$parameter = $c[0];
if($parameter=='logout'){
  include 'pages/logout.php';
  exit;
}

# ========================================================
# INCLUDE INSHO STYLES
# ========================================================
$insho_styles = $online_version ? 'insho_styles.php' : '../insho_styles.php';
include $insho_styles;
include 'jwd_styles.php';
include 'include/meme.php';
include 'include/insho_functions.php';
include 'include/img_icon.php';

# ========================================================
# ROOM PROPERTIES
# ========================================================
# null = 0 = belum aktif
# -1 = selesai (auto jika tidak diantara awal dan akhir )
# 1 = aktif
# ========================================================
$id_room = $_SESSION['dipa_id_room'] ?? '';
$status_room = '';
if($username){
  if($id_room){
    include 'room_vars.php';
    // include 'room_data.php';
    include 'wars_data.php';
  }else{
    if($password){
      $parameter = 'pilih_room';
    }
  }
}
// $id_room = 1; //zzz
// $nama_room = 'Matematika Informatika'; //zzz
// $room = 'MAFI'; //zzz
// $status_room = 0; //closed zzz
// $div_alert_closed = div_alert('danger',meme('closed',6)."<hr>Maaf, room ini udah ditutup. Sampai jumpa di room selanjutnya!");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>DIPA Joiner</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

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
  <style>
    .foto-ilustrasi{
      height: 150px;
      width: 150px;
      object-fit: cover;
      border: solid 1px #ccc;
      box-shadow: 0 0 3px gray;
      border-radius: 50%
    }
    .section-title h2 {font-size: 22px !important; color: #ac5807}

    section {
      margin-top: 60px;
      padding: 60px 0 !important;
    }
    .btop{border-top: solid 1px #ccc}
    <?php if($dm) echo '.debug{display:inline; background:yellow; color: blue}'; ?>
  </style>
</head>

<body>

  <?php // include 'pages/header.php'; ?>
  <?php 
    if(!$is_login || $id_room) include 'pages/header.php'; 
  ?>
  <?php if(!$is_login and $parameter=='') include 'pages/hero.php'; ?>
  <main id="main">
    <section>
      <div class="container">
        <?php include 'routing.php'; ?>
      </div>
    </section>
  </main>
  <?php include 'update_points.php'; ?>
  <?php include 'debug.php'; ?>
  <?php include 'pages/footer.php'; ?>
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>

<?php include 'include/js_btn_aksi.php'; ?>
