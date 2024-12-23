<?php
set_h2('Verifikasi WA', 'Verifikasi WA via whatsapp API');
$fkhusus = div_alert('danger', '
Maaf, fitur ini hanya bisa diakses oleh Instruktur | 
<a href="?login">Login</a>
');

$get_username = $_GET['username'] ?? die($fkhusus);
$get_kelas = $_GET['kelas'] ?? die($fkhusus);
$get_no_wa = $_GET['no_wa'] ?? die($fkhusus);

if ($get_kelas == 'MITRA') {
  echo div_alert('danger', "Belum ada handler untuk kelas $get_kelas");
} elseif ($get_kelas == 'INSTRUKTUR') {
  echo div_alert('danger', "Belum ada handler untuk kelas $get_kelas");
} elseif (!isset($id_role) || $id_role != 2) {
  echo $fkhusus;
} else {
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
