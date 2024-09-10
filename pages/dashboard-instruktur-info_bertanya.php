<?php
if (!$room_count['count_bertanya']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada satu pun Peserta Yang Bertanya</div>
    <div>$img_warning</div>
  ";
  $notif_type = 'warning';
} elseif ($room_count['count_bertanya_verified'] == $room_count['count_bertanya']) {
  $ui_check = "
    <div class='f12 abu mb1'>Belum ada lagi Peserta Yang Bertanya</div>
    <div>$img_check</div>
  ";
  $notif_type = 'success';
} else {
  $ui_check = "
    <div class='f12 abu mb1'>Masih ada " .
    ($room_count['count_bertanya'] - $room_count['count_bertanya_verified']) .
    " pertanyaan harus Anda bahas bersama</div><div>$img_warning</div>
  ";
  $notif_type = 'danger';
}
echo div_alert("$notif_type tengah", "
  <a href='?questions'>
    <div>$room_count[count_bertanya_verified] dari $room_count[count_bertanya] pertanyaan terjawab</div>
    $ui_check
  </a>
");
