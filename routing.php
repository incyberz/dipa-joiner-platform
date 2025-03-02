<?php
if ($parameter == 'login' and $is_login) die('<script>location.replace("?")</script>');


if ($parameter != 'ubah_password') {
  if ($is_login and $password == '') {
    if (!isset($_SESSION['dipa_master_username'])) {
      echo '<script>location.replace("?ubah_password")</script>';
      exit;
    }
  }
}


if (!$status and $parameter != 'verifikasi_wa' and $is_login and $parameter != 'ubah_password') {
  $custom_sebagai = $custom[$sebagai] ?? $sebagai;
  $custom_kelas = $custom[$kelas] ?? $kelas;
  echo "
    <section id=section_routing>
      <div class='wadah gradasi-kuning p4'>
        <div class=sub_form>Routing Exceptions - Unverified Status as $Trainer</div>
        <p>Halo $nama_peserta!</p>
        <p>Kamu sudah tercatat sebagai <u>$custom_sebagai</u> pada kelas <u>$custom_kelas</u>. </p><p>Untuk $custom_sebagai baru wajib diverifikasi oleh Master $Trainer yaitu Dev-Team (Bapak Iin Sholihin, dan tim)</p>
        <hr>
        <div class='alert alert-danger'>
          Status <i class=darkblue>Akun $custom_sebagai</i> Anda belum terverifikasi.  
          <a class='btn btn-primary w-100 mt2' href='?verifikasi_wa&dari=routing_verifikasi_wa_instruktur'>Verifikasi Whatsapp</a>
        </div>
      </div>
      <a href='?logout'>Logout</a>
    </section>
  ";
  exit;
}


if ($id_room and $status_room === null and $id_role == 2 and $parameter != 'aktivasi_room') {
  # ============================================================
  # WAJIB AKTIVASI SETELAH MEMBUAT ROOM DI TAHAP AWAL
  # ============================================================
  jsurl('?aktivasi_room');
} elseif ($parameter != 'manage_ta' and (!$ta_awal || !$ta_akhir)) {
  # ============================================================
  # WAJIB MANAGE TA JIKA INVALID 
  # ============================================================
  if ($id_role == 2) echo div_alert('danger', "TA $ta_aktif invalid setting. <a href=?manage_ta>Manage TA</a>");
  // jsurl('?manage_ta');
} elseif (!$sesi_aktif and $parameter != 'manage_sesi_aktif' and $id_room) {
  if ($id_role == 2 and $room['status'] == 100) echo div_alert('danger', "Sesi Aktif pada TA $ta_aktif invalid. <a href=?presensi>Manage Sesi Presensi</a>");
  // jsurl('?manage_sesi_aktif');
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
    # ============================================================
    # KESEMPATAN SATU KALI JOIN KELAS PER SEMESTER
    # ============================================================
    include 'pages/join_kelas.php';
  } else {
    if ($id_room) {
      include 'includes/ontops.php';
      if ($id_role == 2) {
        include 'includes/form_ubah_ta.php';
        include 'includes/form_target_kelas.php';
      }
    }
    include $konten;
  }
}
