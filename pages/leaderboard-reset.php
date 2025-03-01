<?php
if (isset($_POST['btn_reset_leaderboard'])) {
  $s = "DELETE FROM tb_poin_weekly WHERE id = '$id_room-$week' ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}
