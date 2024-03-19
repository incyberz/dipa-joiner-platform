<?php
if ($parameter == 'login' and $is_login) die('<script>location.replace("?")</script>');
// echo "<span class=debug>id_peserta:<span id=id_peserta>$id_peserta</span> | nama_peserta:<span id=nama_peserta>$nama_peserta</span> | </span>";

if (!$status and $parameter != 'verifikasi_wa' and $is_login) {
  echo "<section><div class='container red small miring gradasi-merah'>Kamu belum verifikasi whatsapp sehingga banyak fitur yang dibatasi. | <a href='?verifikasi_wa'>Verifikasi</a></div></section>";
}


switch ($parameter) {
  case '':
  case 'home':
    $konten = 'pages/home.php';
    break;
    // case 'sections': $konten = 'pages/sections.php'; break;
  default:
    $konten = $parameter;
}

if (!file_exists($konten)) $konten = "pages/$konten.php";
if (!file_exists($konten)) {
  $konten_admin = "pages/admin/$parameter.php";
  include file_exists($konten_admin) ? $konten_admin : 'na.php';
} else {
  if (!$punya_profil) {
    if ($parameter == 'login' || $parameter == 'upload_profil' || !$is_login) {
      // hide ask upload profil
    } else {
      if ($password and $id_room) {
        include 'pages/belum_punya_profil.php';
      }
    }
  }

  // if($profil_ok==-1 || !$profil_ok){
  //   if($is_login and $password and $id_room and $parameter!='login' and $parameter!='upload_profil'){
  //     include 'pages/belum_punya_profil.php';
  //   }
  // }

  if ($username and !$kelas) {
    include 'pages/join_kelas.php';
  } else {
    include $konten;
  }
}

if ($parameter != 'ubah_password') {
  if ($is_login and $password == '') {
    if (!isset($_SESSION['dipa_master_username'])) {
      echo '<script>location.replace("?ubah_password")</script>';
      exit;
    }
  }
}
