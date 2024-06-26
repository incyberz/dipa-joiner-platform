<?php
if ($best_code == 'room_player' || $best_code == 'room_kelas') {
  // echo div_alert('danger', "Belum ada handle untuk best_code: ROOM_PLAYER || ROOM_KELAS");
  include 'update_room_player_rank.php';
  exit;
}
