<?php
function href_wa(
  $phone,
  $ingin,
  $header_info = null,
  $monospace = false,
  $all_bold = false,
  $nama_tujuan = null,
  $gender_tujuan = null,
  $nama_pengirim = null
) {
  $Bapak = '';
  if (strtolower($gender_tujuan) == 'l') $Bapak = 'Bapak';
  if (strtolower($gender_tujuan) == 'p') $Bapak = 'Ibu';
  $datetime = date('d F, Y, H:i:s');
  $link_encoded = urlencode(get_current_url());
  $nama_tujuan = $nama_tujuan ? $nama_tujuan : 'Trainer';
  $text_wa = "Yth. $Bapak $nama_tujuan, saya $nama_pengirim $ingin. Terimakasih.%0a%0aLink:%0a$link_encoded%0a%0aFrom: Gamified LMS System, $datetime";

  if ($header_info) {
    $text_wa = "=======================%0a$header_info%0a=======================%0a$text_wa";
  }
  if ($monospace) {
    $text_wa = "```$text_wa```";
  }
  if ($all_bold) {
    $text_wa = "*$text_wa*";
  }
  return "https://api.whatsapp.com/send?phone=$phone&text=$text_wa";
}
