<style>
  form label {margin: ; color: #555}
  form small {color: gray; }
</style>
<?php
$pesan_join='';
$nama = '';
$username = '';
$select_kelas = '';
$kelas_new = '';
if(isset($_POST['btn_join'])){

  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  function clean($a){ return str_replace('"','',str_replace('\'','',$a));}

  $nama = clean($_POST['nama']);
  $username = clean($_POST['username']);
  $select_kelas = clean($_POST['select_kelas']);
  $kelas_new = clean($_POST['kelas_new']);

  $kelas = $select_kelas=='new' ? $kelas_new : $select_kelas;

  if($_POST['kelas_new']!='null'){ // insert new class
    $s = "INSERT INTO tb_kelas (kelas) VALUES ('$kelas_new') ON DUPLICATE KEY UPDATE status=1";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  }
  
  $s = "SELECT 1 FROM tb_peserta WHERE username='$username'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $pesan_join = "<div class='alert alert-danger' data-aos='fade-left'>Nickname <b><u>$username</u></b> sudah ada. Silahkan tambahkan nickname Anda dengan angka, nama tengah, atau nama belakang kamu (tanpa spasi) agar tetap mudah diingat.</div>";

  }else{
    
    $s = "INSERT INTO tb_peserta 
    (username,nama,kelas,status) VALUES 
    ('$username','$nama','$kelas','0')";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo div_alert('success',"Join sebagai $as dengan nickname: <b>$username</b> berhasil.<hr><span class='tebal darkred'>Mohon tunggu! redirecting...</span>");

    $pesan = div_alert('success',"Join sebagai $as dengan nickname: <b>$username</b> berhasil.<hr><span class='darkblue'>Silahkan Anda login dengan username yang barusan Anda buat.
    <ul>
      <li><b class=abu>Username:</b> $username</li>
      <li><b class=abu>Password:</b> $username</li>
    </ul>
    <a class='btn btn-primary btn-sm btn-block' href='?login&username=$username'>Menuju Login Page</a>
    ");

    $pesan = urlencode($pesan);
    
    echo "<script>setTimeout(()=>location.replace('?pesan_show&pesan=$pesan'),1000)</script>";
    exit;
  }

}






$s = "SELECT kelas FROM tb_kelas WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$option_kelas = '';
while ($d=mysqli_fetch_assoc($q)) {
  $selected = $d['kelas']==$select_kelas ? 'selected' : '';
  $option_kelas.= "<option $selected>$d[kelas]</option>";
}

$hideit_btn_join = ($nama!='' and $username!='' and $select_kelas!='0') ? '' : 'hideit';

?>
<div class="section-title" data-aos="fade-up">
  <h2>Join</h2>
  <p>Silahkan Anda Join sebagai <span class='proper'><?=$as?></span></p>
  <div class="mt3 mb4">
    <img src='assets/img/icons/<?=$as?>.png' alt='img as' class='foto-ilustrasi'>
  </div>
  <?=$pesan_join?>
</div>

<div class="wadah gradasi-hijau" data-aos="fade-up" data-aos-delay="150">
  <form method=post>
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input class='form-control input_isian' type="text" id='nama' name='nama' required maxlength=20 minlength=3 value='<?=$nama?>'>
    </div>
    <div class="form-group">
      <label for="username">Nama Panggilan / Nickname</label>
      <input class='form-control input_isian' type="text" id='username' name='username' required maxlength=20 minlength=3 value='<?=$username?>'>
      <small class='miring'>Max 10 huruf dan tanpa spasi</small>
    </div>
    <div class="form-group">
      <label for="select_kelas">Kelas</label>
      <select name="select_kelas" id="select_kelas" class="form-control">
        <option value="0">--Pilih--</option>
        <?=$option_kelas?>
        <option value="new" class='kecil miring abu'>buat kelas baru...</option>
      </select>
      <div class="wadah hideit mt2" id="blok_new_kelas">
        <label for="kelas_new">Silahkan masukan kelas baru Anda:</label>
        <input class='form-control input_isian' type="text" id='kelas_new' name='kelas_new' placeholder='kelas baru' value='null' required maxlength=20 minlength=4>
        <small class=miring>Max 20 karakter tanpa spasi</small>
      </div>

    </div>

    <div class="form-group <?=$hideit_btn_join?>" id="blok_btn_join">
      <button class="btn btn-primary btn-block" name=btn_join>Join</button>
    </div>
  </form>
</div>

<!-- <div class="tengah kecil mt3" data-aos="fade-up" data-aos-delay="300">Punya akun? Silahkan <a href="?login">Login</a></div> -->

<script>
  $(function(){
    $('#select_kelas').change(function(){
          
      let val = $(this).val();
      if(val=='0'){
        $('#blok_new_kelas').hide();
        $('#kelas_new').val('null');
        $('#blok_btn_join').fadeOut();
      }else{
        $('#blok_btn_join').fadeIn();
        $('#kelas_new').val('');
        if(val=='new'){
          $('#blok_new_kelas').show();
          console.log(val, $('#blok_new_kelas').val());
        }else{
          $('#blok_new_kelas').hide(); //pilih kelas yg sdh ada
          $('.input_isian').keyup();
          $('#kelas_new').val('null');
        }
      }
    })

    $('.input_isian').keyup(function(){
      let link_wa = 'https://api.whatsapp.com/send?phone=6287729007318&text=*Verification Link Request*%0a%0a';
      let nama = $('#nama').val();
      let username = $('#username').val();
      let kelas = $('#select_kelas').val()=='new' ? $('#kelas_new').val() : $('#select_kelas').val();

      let href = `${link_wa}&nama=${nama}&username=${username}&kelas=${kelas}`;

      $('#link_btn_join').prop('href',href);
    })
  })
</script>