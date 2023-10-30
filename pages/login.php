<?php
$pesan = '<p>Silahkan masukan Username dan password untuk login. Untuk password awal adalah sama dengan username Anda.</p>';
$username = '';
$password = '';


if(isset($_POST['btn_login_mhs'])){
  $username = clean_sql($_POST['username']);
  $password = clean_sql($_POST['password']);

  $sql_password = $username==$password ? 'password is null' : "password=md5('$password')";
  $s = "SELECT id,id_role from tb_peserta WHERE username='$username' and $sql_password";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==1){
    $d=mysqli_fetch_assoc($q);
    $_SESSION['dipa_username'] = $username;
    $_SESSION['dipa_role'] = $d['id_role'];
    $_SESSION['dipa_id_peserta'] = $d['id'];
    echo '<script>location.replace("?")</script>';
    exit;
  }else{
    $pesan = div_alert('danger','Maaf, username dan password tidak tepat. Silahkan coba kembali!');
    // $pesan .= " username:$username password:$password"; //zzz debug
  }
}
?>
<style>
  .full{
    display:flex;
    height: 100vh;
  }
  .form-login{
    max-width: 400px;
    margin:auto;
  }
</style>
<div class="full" data-aos='fade-up'>
  <div class="wadah gradasi-biru form-login p-4">
    <h3>Login Peserta</h3>
    <?=$pesan?>
    <hr>
    <form method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" minlength=3 maxlength=50 required id="username" name="username" value="<?=$username?>">
      </div>

      <div class="form-group">
        <label for="username">Password</label>
        <input type="password" class="form-control" minlength=3 maxlength=50 required id="password" name="password" value="<?=$password?>">
      </div>

      <div class="form-group">
        <button class='btn btn-primary btn-block' name='btn_login_mhs'>Login</button>
      </div>      
    </form>

    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="300">Belum punya akun? Silahkan <a href="?join"><b>Join</b></a></div>
    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="300">Lupa password? <a href="?reset_password"><b>Reset Password</b></a></div>

  </div>
</div>
