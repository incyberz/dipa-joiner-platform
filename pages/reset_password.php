<?php
$pesan = '<p>Reset Password akan dikirim ke Whatsapp Instruktur dan akan mengembalikan password menjadi sama dengan username.</p>';
$username = '';
// $no_wa = '';

$opt_kelas = '<option value=NULL>--Pilih--</option>';
$s = "SELECT kelas FROM tb_kelas";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while ($d=mysqli_fetch_assoc($q)) $opt_kelas.="<option>$d[kelas]</option>";


if(isset($_POST['btn_reset'])){
  $username = clean_sql($_POST['username']);
  $no_wa = clean_sql($_POST['no_wa']);

  $sql_password = $username==$no_wa ? 'no_wa is null' : "no_wa=md5('$no_wa')";
  $s = "SELECT id,id_role from tb_peserta WHERE username='$username' and $sql_password";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==1){
    $d=mysqli_fetch_assoc($q);
    $_SESSION['dipa_username'] = $username;
    $_SESSION['dipa_id_role'] = $d['id_role'];
    $_SESSION['dipa_id_peserta'] = $d['id'];
    echo '<script>location.replace("?")</script>';
    exit;
  }else{
    $pesan = div_alert('danger','Maaf, username dan no_wa tidak tepat. Silahkan coba kembali!');
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
    <h3>Reset Password</h3>
    <?=$pesan?>
    <hr>
    <form method="post" action="?verifikasi_wa&untuk=reset_password">
      <input type="hidden" name=dari value=reset_password>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" minlength=3 maxlength=50 required id="username" name="username" value="<?=$username?>">
      </div>
      <div class="form-group">
        <label for="kelas">Kelas</label>
        <select class="form-control" name="kelas"><?=$opt_kelas?></select>
      </div>

      <div class="form-group">
        <button class='btn btn-primary btn-block' name='btn_reset'>Reset via Whatsapp</button>
      </div>      
    </form>

    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="300">Belum punya akun? Silahkan <a href="?join"><b>Join</b></a></div>
    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="400">Bac to: <a href="?login"><b>Login</b></a></div>

  </div>
</div>
