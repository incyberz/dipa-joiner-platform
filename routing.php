<?php
if ($parameter == 'login' and $is_login) die('<script>location.replace("?")</script>');
// echo "<span class=debug>id_peserta:<span id=id_peserta>$id_peserta</span> | nama_peserta:<span id=nama_peserta>$nama_peserta</span> | </span>";

if (!$status and $parameter != 'verifikasi_wa' and $is_login and $parameter != 'ubah_password') {
  echo "
    <section>
      <div class='wadah gradasi-kuning small miring  p4'>
        <div class=sub_form>Routing Exceptions - Unverified Status as Instruktur</div>
        <p>Halo $nama_peserta!</p>
        <p>Kamu sudah tercatat sebagai <u>$sebagai</u> pada kelas <u>$kelas</u>. Untuk $sebagai baru wajib diverifikasi oleh Master Instruktur yaitu Dev-Team (Bapak Iin Sholihin, dan tim)</p>
        <hr>
        <div class='alert alert-danger'>
          Status <i class=darkblue>Akun $sebagai</i> Anda belum terverifikasi. | 
          <a href='?verifikasi_wa&dari=routing_verifikasi_wa_instruktur'>Verifikasi Whatsapp $sebagai</a>
        </div>
      </div>
      <a href='?logout'>Logout</a>
    </section>
  ";
  exit;
}

# ============================================================
# WAJIB AKTIVASI SETELAH MEMBUAT ROOM DI TAHAP AWAL
# ============================================================
if ($id_room and $status_room === null and $id_role == 2 and $parameter != 'aktivasi_room') {
  jsurl('?aktivasi_room');
}


switch ($parameter) {
  case '':
  case 'home':
    $konten = 'pages/home.php';
    break;
  case 'sync':
    $konten = 'pages/sync/sync.php';
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
