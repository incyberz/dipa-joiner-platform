<?php
if (isset($_GET['logout'])) {
  // delete cookie
  setcookie($dipa_cookie, '', time() - 3600, '/');

  unset($_SESSION['dipa_username']);
  unset($_SESSION['dipa_id_role']);
  unset($_SESSION['dipa_id_peserta']);
  unset($_SESSION['dipa_id_room']);


  echo '<script>location.replace("?")</script>';
  exit;
}
