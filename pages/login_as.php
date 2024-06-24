<div class="section-title" data-aos="fade-up">
  <h2>Login As</h2>
  <!-- <p>This page is ready to code ...</p> -->
</div>

<?php
instruktur_only();
// if($id_role==1) die(erid('roles'));
$judul = 'Login As';

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';

if (isset($_GET['unlog'])) {
  $_SESSION['dipa_username'] = $_SESSION['dipa_master_username'];
  unset($_SESSION['dipa_master_username']);

  echo div_alert('success', 'Unlog success.');
  echo '<script>location.replace("?")</script>';
  exit;
}

if ($id_role == 2 || isset($_SESSION['dipa_master_username'])) {
  $new_username = $_GET['username'] ?? '';
  if (!$new_username) {
    $get_id_peserta = $_GET['id_peserta'] ?? '';
    if (!$get_id_peserta) {
      // session_destroy();
      echo div_alert('danger', "Tidak dapat handle multiple Login As. Silahkan login ulang!");
      // jsurl('', 2000);
    }
    $s = "SELECT username FROM tb_peserta WHERE id=$get_id_peserta";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) {
      die('Page ini membutuhkan parameter id_peserta atau username');
    } else {
      $d = mysqli_fetch_assoc($q);
      $new_username = $d['username'];
    }
  }
  $_SESSION['dipa_master_username'] = $_SESSION['dipa_username'];
  $_SESSION['dipa_username'] = $new_username;
  echo "<script>alert('Login as $new_username sukses.')</script>";
  echo '<script>location.replace("?")</script>';
  exit;
}



// if(isset($_SESSION['dipa_master_username'])){
//   echo "<a href='?login_as&unlog'>Back to Master Username</a>";
// }





















?>

<div class="alert alert-success" data-aos="fade-up" aos-delay=150>
  <h4>New Session Started</h4>
  <ul>
    <li>Login as : <?= $new_username ?></li>
  </ul>
  <button class='btn btn-primary btn-block' onclick='location.reload()'>Refresh</button>
</div>