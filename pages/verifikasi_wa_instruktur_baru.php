<?php
$tanggal =  date('D, M d, Y, H:i:s');
$link_encoded = urlencode("$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]/$_SERVER[SCRIPT_NAME]?verifikasi_wa_for_instruktur&username=$username&kelas=$kelas&no_wa=$no_wa");
$custom_sebagai = $custom[$sebagai] ?? $sebagai;
$SEBAGAI = strtoupper($custom_sebagai);
$Instruktur = $custom['instruktur'] ?? 'Instruktur';
$DIPAJoiner = $custom['DIPA Joiner'] ?? 'DIPA Joiner';
$text_wa = "*REQUEST $SEBAGAI BARU*%0a%0aYth. Master $Instruktur $DIPAJoiner ($ops[nama]),%0a%0aSaya $nama, mengajukan Request sebagai *$SEBAGAI BARU* atas username: *$username* dengan nomor-wa-aktif: *$no_wa*. Mohon untuk segera diverifikasi agar saya dapat melanjutkan ke Manage Akun selanjutnya. Terimakasih.%0a%0aLink:%0a$link_encoded %0a%0a [$DIPAJoiner Apps, $tanggal]";
$text_wa_show = str_replace('%0a', '<br>', $text_wa);
$text_wa_show = str_replace('%3A', ':', $text_wa_show);
$text_wa_show = str_replace('%2F', '/', $text_wa_show);
$text_wa_show = str_replace('%3F', '?', $text_wa_show);
$text_wa_show = str_replace('%26', '&', $text_wa_show);
$text_wa_show = str_replace('%3D', '=', $text_wa_show);

set_h2('Verif WA', "Berikut adalah link dan pesan yang akan diteruskan ke Master $Instruktur");

?>

<div class="wadah gradasi-hijau" data-aos-zzz="fade-up" data-aos-delay="150">
  <div class="mb1 f12 abu">Preview Pesan:</div>
  <div class="f12 bordered bg-white p1">
    <?= $text_wa_show ?>
  </div>
  <div class="form-group">
    <a target="_blank" href='https://api.whatsapp.com/send?phone=<?= $ops['whatsapp'] ?>&text=<?= $text_wa ?>' class="btn btn-primary btn-block">Kirim Pesan Verifikasi</a>
  </div>
  <hr>
  <div class="tengah f14 abu">
    <a href="?">Home</a> |
    <a href="?logout">Logout</a>

  </div>
</div>