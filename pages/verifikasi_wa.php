<?php
$debug = '';
$hide_form = 0;
$hide_section = 0;
$form = '';
$dari = '';
$no_wa = '';
$untuk = $_GET['untuk'] ?? '';
if(!$is_login AND $untuk=='') die('<script>location.replace("?")</script>');
$pesan = 'Untuk mengakses semua fitur kamu harus update dengan nomor whatsapp yang aktif.';

$caption = 'Verifikasi';
if($id_peserta==''){ // belum login
  $debug.= "
  <span class=debug><span id=is_login>is_login:$is_login</span></span>
  ";

  if(isset($_POST['username'])){
    $username = $_POST['username'];
    $kelas = $_POST['kelas'];
    $dari = $_POST['dari'];

    if($dari=='reset_password') $caption = 'Reset Password';

    $pesan = "Request reset password dari username: <b class=darkblue>$username</b> kelas <b class=darkblue>$kelas</b>";
    $s = "SELECT no_wa,nama FROM tb_peserta WHERE username='$username' AND kelas='$kelas'";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)==0){
      $link_back = $dari=='' ? '' : " | <a href='?$dari'>Kembali</a>";
      $pesan.= div_alert('danger', "Maaf, data untuk username $username kelas $kelas tidak ditemukan. $link_back");
      $hide_form = 1;
    }else{
      $d=mysqli_fetch_assoc($q);
      $no_wa = $d['no_wa'];
      $nama = $d['nama'];

      if($no_wa==''){
        $pesan.= div_alert('info',"Anda belum memasukan Nomor Whatsapp. Silahkan masukan dahulu nomor yang aktif.");
      }else{
        $pesan.= div_alert('info',"Data whatsapp sudah ada, silahkan klik Verifikasi/Reset. Jika nomor aktif saat ini berbeda maka silahkan input kembali.");
      }

      

    }    
  }elseif(isset($_POST['btn_submit'])){
    $dari = $_POST['dari'];

    if($_POST['dari']=='reset_password'){

      $username = $_POST['username_reset'];
      $kelas = $_POST['kelas'];
      $no_wa = $_POST['no_wa'];
      $nama = $_POST['nama'];
      include 'reset_password_confirm.php';
      $hide_section=1;
      
    }else{
      // verifikasi normal
      echo "VERIFIKASI NORMAL";
    }
  }else{
    $pesan.= div_alert('danger','No POST request detected.');
    exit;
  }


}

if(!$hide_form){
  $form = "
  <form method=post>
    <input class=debug name=nama value='$nama'>
    <input class=debug name=dari value='$dari'>
    <input class=debug name=username_reset value='$username'>
    <input class=debug name=kelas value='$kelas'>
    <div class='wadah gradasi-hijau' data-aos='fade-up' data-aos-delay='200'>
      <label for='no_wa' class='tengah mb1'>Nomor WhatsApp <span class='kecil abu miring'>* yang aktif</span></label>
      <input type='text' class='form-control tengah' minlength=11 maxlength=14 id=no_wa autocomplete=off style='color:gray' name=no_wa required value='$no_wa'>
      <div class='tengah consolas' style='font-size:30px' id=no_wa2>628X-XXX-XXX-XXX</div>
      <div class='tengah consolas red' style='font-size:10px' id=no_wa_invalid>awali dg '08...' atau '62...'</div>
      <div>
        <button class='btn btn-primary btn-block' id=btn_verifikasi name=btn_submit>$caption</button>
      </div>
    </div>
  </form>
  ";
}

if(!$hide_section){
?>
<section>
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Verifikasi WA</h2>
      <p><?=$pesan?></p>
    </div>
      <?=$form?>
      <?=$debug?>
    </div>
  </div>
</section>
<?php } ?>


<script>
  $(function(){
    (function($) {
      $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
          if (inputFilter(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
          } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
          } else {
            this.value = '';
          }
        });
      };
    }(jQuery));
    
    $("#no_wa").inputFilter(function(value) { return /^\d*$/.test(value); });
    $("#no_wa").keyup(function() { 
      let val = $(this).val();
      let val2 = val.substring(0,4)
        + '-' + val.substring(4,7)
        + '-' + val.substring(7,10)
        + '-' + val.substring(10,14);
      val2 = val2=='' ? '-' : val2;
      $('#no_wa2').text(val2);


      if(val.substring(0,2)=='08' || val.substring(0,2)=='62'){
        $('#no_wa_invalid').text('');
        if(val.substring(0,2)=='08'){
          val = '628' + val.substring(2,14);
          $('#no_wa').val(val);
        }
      }else{
        $('#no_wa_invalid').text('awali dg "08..." atau "62..."');
        if(val.length>2){
          $(this).val('');
        }
        return;
      }




      if(val.length>10){
        $('#btn_verifikasi').fadeIn();
      }else{
        $('#btn_verifikasi').fadeOut();
      }
    });
  })
</script>