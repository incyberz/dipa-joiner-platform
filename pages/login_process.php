<?php
if(isset($_POST['btn_login_peserta'])){
  $username = clean_sql($_POST['username']);
  $password = clean_sql($_POST['password']);

  $sql_password = $username==$password ? 'password is null' : "password=md5('$password')";
  $s = "SELECT id,id_role from tb_peserta WHERE username='$username' and $sql_password";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==1){
    $d=mysqli_fetch_assoc($q);
    $_SESSION[$dipa_cookie] = $username;
    $_SESSION['dipa_id_role'] = $d['id_role'];
    $_SESSION['dipa_id_peserta'] = $d['id'];

    # ========================================================
    # SET COOKIE
    # ========================================================
    setcookie($dipa_cookie, $username, time() + (86400), "/"); // 86400 = 1 day

    echo '<script>location.replace("?")</script>';
    exit;
  }else{
    $pesan_login = div_alert('danger','Maaf, username dan password tidak tepat. Silahkan coba kembali!');
  }
}