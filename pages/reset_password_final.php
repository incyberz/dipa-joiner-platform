<?php
unset($_SESSION['dipa_username']);
unset($_SESSION['dipa_id_role']);
unset($_SESSION['dipa_id_peserta']);
?>

<div class="section-title" data-aos="fade-up">
  <h2>Reset</h2>
  <p>Reset Password Final. Jika key Anda benar maka sistem akan mereset password Anda</p>
</div>

<?php
$key = $_GET['key'] ?? die(div_alert('danger', 'Key untuk reset invalid.'));

$s = "SELECT * FROM tb_reset WHERE terpakai is null and md5(kunci)='$key'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){

  $d = mysqli_fetch_assoc($q);

  $s = "UPDATE tb_peserta SET no_wa='$d[no_wa]', status=1, password=null WHERE username='$d[username]'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $s = "UPDATE tb_reset SET terpakai=1 WHERE md5(kunci)='$key'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


  echo "
  <div class='alert alert-success tengah' data-aos=fade data-aos-delay=1000>
    Reset Password berhasil. Silahkan login menggunakan password sama dengan username Anda. | <a href='?login'>Login</a>
  </div>
  ";
}else{

  //terpakai oleh whatsapp preview link
  //cek apakah password sudah null
  $s = "SELECT 1 FROM tb_reset a 
  JOIN tb_peserta b ON a.username=b.username 
  WHERE md5(a.kunci)='$key' AND b.password is null";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    echo "
    <div class='alert alert-success tengah' data-aos=fade data-aos-delay=1000>
      Password Anda telah direset. Silahkan login menggunakan password sama dengan username Anda. | <a href='?login'>Login</a>
    </div>
    ";
  }else{
    echo "
    <div class='alert alert-danger tengah' data-aos=fade data-aos-delay=1000>
      Maaf, sepertinya key invalid atau telah expire. | Back to: <a href='?reset_password'>Reset Password</a> | <a href='?login'>Login</a>
    </div>
    ";
  }
}
