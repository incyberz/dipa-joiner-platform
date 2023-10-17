<?php
if(isset($_GET['logout'])){
  unset($_SESSION['dipa_username']);
  unset($_SESSION['dipa_role']);
  unset($_SESSION['dipa_id_peserta']);
  echo '<script>location.replace("?")</script>';
  exit;
}
