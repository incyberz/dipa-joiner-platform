<?php
if (isset($_POST['btn_join'])) {
  $as = $_POST['as'] ?? $_GET['as'];
  if (!$as) stop("Join process membutuhkan variabel as (sebagai)");

  $_POST = clean_post($_POST);

  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $select_kelas = $as == 'peserta' ? $_POST['select_kelas'] : strtoupper($as);

  $s = "SELECT 1 FROM tb_peserta WHERE username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $pesan_login_error = "<div class='alert alert-danger' data-aos='fade-left'>Nickname <b><u>$username</u></b> sudah diambil. Silahkan tambahkan nickname Anda dengan angka, nama tengah, atau nama belakang (tanpa spasi atau karakter khusus).</div>";
  } else { // input username sudah unik

    // default status $Peserta baru = aktif
    $status = 1;
    $id_role = 1; // default as $Peserta
    if ($as != 'peserta') {
      $status = 0; // perlu verifikasi untuk $Trainer, pro, mitra baru
      if ($as == 'instruktur') {
        $id_role = 2;
      } elseif ($as == 'praktisi') {
        $id_role = 3;
      } elseif ($as == 'mitra') {
        $id_role = 4;
      } else {
        die('Undefined role at processors.');
      }
    }

    // add $Peserta
    $s = "INSERT INTO tb_peserta 
      (username,nama,status,id_role) VALUES 
      ('$username','$nama','$status',$id_role) 
      ON DUPLICATE KEY UPDATE date_created=CURRENT_TIMESTAMP 
      ";
    echo '<pre>';
    print_r($s);
    echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
    exit;
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Insert $as baru sukses...");

    // get id_peserta
    $s = "SELECT id FROM tb_peserta where username='$username'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $id_peserta = $d['id'];
    echo div_alert('info', 'Getting new id_peserta sukses...');

    // assign kelas $Peserta
    $s = "INSERT INTO tb_kelas_peserta 
      (id_peserta,kelas) VALUES 
      ('$id_peserta','$select_kelas')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Assign $Peserta baru ke kelas <u>$select_kelas</u> sukses...");





    echo div_alert('success', "Semua proses join selesai.<hr><span class='tebal darkred'>Mohon tunggu! redirecting...</span>");

    $pesan = div_alert('success', "Join sebagai $as dengan nickname: <b>$username</b> berhasil.<hr><span class='darkblue'>Silahkan Anda login dengan username yang barusan Anda buat.
      <ul>
        <li><b class=abu>Username:</b> $username</li>
        <li><b class=abu>Password:</b> $username</li>
      </ul>
      <a class='btn btn-primary btn-sm btn-block' href='?login&username=$username'>Menuju Login Page</a> 
      ");

    $pesan = urlencode($pesan);

    echo "<script>setTimeout(()=>location.replace('?pesan_show&pesan=$pesan'),1000)</script>";
    exit;
  }
} elseif ($_POST) {
  echo '<pre>';
  print_r($_POST);
  echo '<b style=color:red>Unhnadler data POST-JOIN: exit(true)</b></pre>';
  exit;
}
