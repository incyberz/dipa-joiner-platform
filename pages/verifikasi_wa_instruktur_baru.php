<?php
$tanggal =  date('D, M d, Y, H:i:s');
$link_encoded = urlencode("https://iotikaindonesia.com/dipa/?verifikasi_wa_for_instruktur&username=$username&kelas=$kelas&no_wa=$no_wa");
$text_wa = "*REQUEST INSTRUKTUR BARU*%0a%0aYt. Master Instruktur DIPA Joiner (Bapak Iin Sholihin),%0aSaya $nama, mengajukan Request sebagai *INSTRUKTUR BARU* atas username: *$username* dengan nomor-wa-aktif: *$no_wa*. Mohon untuk segera diverifikasi agar saya dapat melanjutkan ke Manage Room. Terimakasih.%0a%0aLink untuk Master Instruktur:%0a$link_encoded %0a%0a [DIPA Joiner Apps, $tanggal]
";
$text_wa_show = str_replace('%0a', ' ', $text_wa);
?>

<div class="section-title" data-aos-zzz="fade-up">
  <h2>Verifikasi Instruktur Baru</h2>
  <p>Berikut adalah link dan pesan yang akan diteruskan ke Master Instruktur</p>
</div>
<div class="wadah gradasi-hijau" data-aos-zzz="fade-up" data-aos-delay="150">
  <div class="form-group">
    <textarea class="form-control" rows="10" disabled><?= $text_wa_show ?></textarea>
  </div>
  <div class="form-group">
    <a href='https://api.whatsapp.com/send?phone=6287729007318&text=<?= $text_wa ?>' class="btn btn-primary btn-block">Kirim Pesan Verifikasi Instruktur Baru</a>
  </div>
</div>