<?php 
if($parameter=='login' and $is_login) die('<script>location.replace("?")</script>');
// echo "<span class=debug>id_peserta:<span id=id_peserta>$id_peserta</span> | nama_peserta:<span id=nama_peserta>$nama_peserta</span> | </span>";

if(!$status and $parameter!='verifikasi_wa' and $is_login){
  echo "<section><div class='container red small miring gradasi-merah'>Kamu belum verifikasi whatsapp sehingga banyak fitur yang dibatasi. | <a href='?verifikasi_wa'>Verifikasi</a></div></section>";
}


switch ($parameter){
  case '':
  case 'home': $konten = 'pages/home.php'; break;
  // case 'sections': $konten = 'pages/sections.php'; break;
  default: $konten = $parameter;
}

if(!file_exists($konten)) $konten="pages/$konten.php";
if(!file_exists($konten)){
  include 'na.php';
}else{
  if(!$punya_profil) {
    if($parameter=='login' || $parameter=='upload_profil' || !$is_login){
      // hide ask upload profil
    }else{
      include 'pages/belum_punya_profil.php';
    } 
  }
  include $konten;
}

if($is_login and $password=='' and $parameter!='ubah_password'){
  if(!isset($_SESSION['dipa_master_username'])){
    echo '<script>location.replace("?ubah_password")</script>';
    exit;
  }
}

