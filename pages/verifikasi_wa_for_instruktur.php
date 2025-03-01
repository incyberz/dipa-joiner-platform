<?php
$get_username = $_GET['username'] ?? die("Undefined GET [username]");
$get_kelas = $_GET['kelas'] ?? die("Undefined GET [kelas]");
$get_no_wa = $_GET['no_wa'] ?? die("Undefined GET [no_wa]");

$s = "SELECT a.* FROM tb_peserta a WHERE username='$get_username'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

if (isset($_POST['btn_approve'])) {
  $s = "UPDATE tb_peserta SET no_wa = '$get_no_wa', status=1 WHERE username='$get_username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  $tanggal =  date('D, M d, Y, H:i:s');
  $DIPAJoiner = $Nama_LMS ?? 'DIPA Joiner';
  $link = urlencode("$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]");
  $link_encoded = urlencode("$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]/$_SERVER[SCRIPT_NAME]?login&username=$get_username");

  $selamat = "*AKUN LMS VERIFIED*%0a%0aSelamat $d[nama]!%0a%0aAkun Anda telah kami verifikasi. Silahkan login ke LMS dan buatlah $Room Baru untuk Anda mengajar. Terimakasih.%0a%0aLink:%0a$link_encoded %0a%0a[$DIPAJoiner Apps, $tanggal]";

  $href_wa = "https://api.whatsapp.com/send?phone=$get_no_wa&text=$selamat";

  echo div_alert('success', "
    Verifikasi sukses.
    <hr>
    <a class='btn btn-primary' target=_blank href='$href_wa'>Reply Selamat !</a>
  ");
  exit;
}


if (!isset($id_role) || $id_role != 2) {
  echo (div_alert('danger', "Maaf, fitur ini hanya bisa diakses oleh $Trainer | <a target=_blank href='?login'>Login</a>"));
} else {
  if ($get_kelas == 'MITRA') {
    echo div_alert('danger', "Belum ada handler untuk kelas $get_kelas");
  } elseif ($get_kelas == 'PRAKTISI') {
    echo div_alert('danger', "Belum ada handler untuk kelas $get_kelas");
  } elseif ($get_kelas == 'INSTRUKTUR') {
    if ($username != $ops['username']) {
      echo div_alert('danger', "Aksi ini hanya dapat dilakukan oleh <span class=darkblue>Username Developer</span>. <hr>Untuk info lebih lanjut silahkan hubungi Developer atau Jika Anda Operator LMS, maka Anda harus melakukan setting pada Custom Configurations.");
    } else {
      # ============================================================
      # VERIFIKASI TRAINER BARU
      # ============================================================
      set_h2('Verif Trainer Baru');
      $eta = eta2($d['date_created']);

      echo "
        <div>
          <div class='wadah gradasi-kuning mx-auto' style=max-width:600px>
            <div class=row>
              <div class='col-6 bold kanan'>
                ID:
              </div>
              <div class=col-6>
                $d[id]
              </div>
              <div class='col-6 bold kanan'>
                Kelas:
              </div>
              <div class=col-6>
                $get_kelas
              </div>
              <div class='col-6 bold kanan'>
                Username:
              </div>
              <div class=col-6>
                $get_username
              </div>
              <div class='col-6 bold kanan'>
                Whatsapp:
              </div>
              <div class=col-6>
                $get_no_wa
              </div>
              <div class='col-6 bold kanan'>
                Created at:
              </div>
              <div class=col-6>
                $d[date_created]
                <div class='f12 abu miring'>$eta</div>
              </div>
            </div>

            <form method=post class='tengah mt2 border-top pt2'>
              <label>
                <input required type=checkbox> Whatsapp diatas valid.
              </label>
              <input name=get_username value=$get_username type=hidden>

              <button class='btn btn-primary w-100 mt2' name=btn_approve value=$get_no_wa>Approve</button>
            </form>
          </div>
        </div>
      ";
    }
  } else {
    # ============================================================
    # ZZZ OLD CODES
    # ============================================================
    $kunci = date('ymdHis');
    $kunci_encrypted = md5($kunci);

    $s = "INSERT INTO tb_reset 
    (username,kelas,no_wa,id_instruktur,kunci) VALUES 
    ('$get_username','$get_kelas','$get_no_wa','$id_peserta','$kunci') 
    ON DUPLICATE KEY UPDATE tanggal=CURRENT_TIMESTAMP
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


    $link_encoded = urlencode("https://iotikaindonesia.com/dipa/?reset_password_final&key=$kunci_encrypted");
    $text_wa = "Halo $get_username%0a%0aUntuk reset password silahkan klik link berikut:%0a%0a $link_encoded";
    $link_wa = "https://api.whatsapp.com/send?phone=$get_no_wa&text=$text_wa";

    echo "<div data-aos=fade-up><a class='btn btn-primary btn-block' href='$link_wa'>Resend Keys</a></div>";
  }
}
