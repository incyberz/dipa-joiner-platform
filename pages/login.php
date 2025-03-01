<?php
$img_home = img_icon('home');
$pesan_login = 'Masukan Username dan password Anda.';
$username = $_GET['username'] ?? '';
$password = $_GET['password'] ?? '';

include 'login_process.php';

if (isset($_COOKIE[$dipa_cookie])) {
  $cookie_username = $_COOKIE[$dipa_cookie];
  $s = "SELECT id,id_role,username from tb_peserta WHERE username='$cookie_username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) == 1) {
    $d = mysqli_fetch_assoc($q);
    $_SESSION[$dipa_cookie] = $d['username'];
    $_SESSION['dipa_id_role'] = $d['id_role'];
    $_SESSION['dipa_id_peserta'] = $d['id'];

    # ========================================================
    # RESET COOKIE
    # ========================================================
    // harus sebelum kode <html>
    // setcookie($dipa_cookie, $username, time() + (86400), "/"); // 86400 = 1 day

    echo '<script>location.replace("?")</script>';
    exit;
  } else {
    // delete cookie
    setcookie($dipa_cookie, '', time() - 3600);
  }
}

?>
<style>
  .full {
    display: flex;
    height: 100vh;
  }

  .form-login {
    max-width: 400px;
    margin: 30px auto;
  }
</style>
<div class="flexy" data-aos='fade-up'>
  <div class="wadah gradasi-biru form-login p-4">
    <div class="flexy flex-between">
      <div><?= $img_login_as ?> </div>
      <div>
        <h3 class="tengah">Login </h3>
      </div>
      <div>
        <a href='?'><?= $img_home ?></a>
      </div>
    </div>
    <p class="tengah">
      <?= $pesan_login ?>
    </p>
    <hr>
    <div class="tengah">
      <img src="<?= $header_logo ?>" alt="header-logo" class="img-fluid">
    </div>
    <hr>
    <form method="post">
      <div class="form-group">
        <input type="text" class="form-control text-center" minlength=3 maxlength=50 required id="username" name="username" value="<?= $username ?>" placeholder="Enter username...">
      </div>

      <div class="form-group">
        <input type="password" class="form-control text-center" minlength=3 maxlength=50 required id="password" name="password" value="<?= $password ?>" placeholder="Enter password...">
      </div>

      <div class="form-group">
        <button class='btn btn-primary btn-block' name='btn_login_peserta'>Login</button>
      </div>
    </form>

    <div class="tengah mt3">Belum punya akun? Silahkan <a href="?join"><b class="proper"><?= $Join ?></b></a></div>
    <div class="tengah mt3">Lupa password? <a href="?reset_password"><b>Reset Password</b></a></div>

    <hr>
    <div class="tengah">
      <a href='?'>Back Home <?= $img_home ?></a>
    </div>
  </div>
</div>