<?php
$tanggal =  date('D, M d, Y, H:i:s');
$link_encoded = urlencode("https://iotikaindonesia.com/dipa/?verifikasi_wa_for_instruktur&username=$username&kelas=$kelas&no_wa=$no_wa");
$text_wa = "*REQUEST RESET PASSWORD*%0a%0aYt. $Trainer DIPA Joiner,%0aSaya $nama, mengajukan Request Reset Password atas username: *$username* kelas *$kelas* dengan nomor-wa-aktif: *$no_wa*. Mohon untuk segera diverifikasi. Terimakasih.%0a%0aLink untuk $Trainer:%0a$link_encoded %0a%0a [DIPA Joiner Apps, $tanggal]
";
$text_wa_show = str_replace('%0a', ' ', $text_wa);
?>

<div class="section-title" data-aos-zzz="fade-up">
  <h2>Reset</h2>
  <p>Setelah Reset Password maka password akan sama dengan username</p>
</div>
<div class="wadah gradasi-hijau" data-aos-zzz="fade-up" data-aos-delay="150">
  <div class="form-group">
    <textarea class="form-control" rows="10" disabled><?= $text_wa_show ?></textarea>
  </div>
  <div class="form-group">
    <a href='https://api.whatsapp.com/send?phone=<?= $trainer['no_wa'] ?>&text=<?= $text_wa ?>' class="btn btn-primary btn-block">Kirim Pesan Reset Password</a>
  </div>
</div>