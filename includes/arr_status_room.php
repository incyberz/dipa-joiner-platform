<?php
$s = "SELECT * FROM tb_status_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$arr_status_room = [];
$arr_status_room_desc = [];
while ($d = mysqli_fetch_assoc($q)) {
  $arr_status_room[$d['status']] = $d['nama'];
  $arr_status_room_desc[$d['status']] = $d['deskripsi'];
}
