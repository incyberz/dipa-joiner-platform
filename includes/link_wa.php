<?php
function href_wa($phone, $text_wa, $header_info = null, $monospace = false, $all_bold = false)
{
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
