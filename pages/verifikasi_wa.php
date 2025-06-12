<?php
$debug = '';
$hide_form = 0;
$hide_section = 0;
$form = '';
$dari = $_GET['dari'] ?? '';
$no_wa = '';
$untuk = $_GET['untuk'] ?? '';
if (!$username and $untuk == '') die('<script>location.replace("?")</script>');
$pesan = 'Untuk mengakses semua fitur kamu harus update dengan nomor whatsapp yang aktif.';


$caption = 'Verifikasi';


if ($id_peserta == '') { // belum login
  if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $kelas = $_POST['kelas'];
    $dari = $_POST['dari'];

    if ($dari == 'reset_password') $caption = 'Reset Password';

    $pesan = "Request reset password dari username: <b class=darkblue>$username</b> kelas <b class=darkblue>$kelas</b>";
    $s = "SELECT 
    a.no_wa,
    a.nama 
    FROM tb_peserta a 
    JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
    JOIN tb_kelas c ON b.kelas=c.kelas  
    WHERE a.username='$username' 
    AND b.kelas='$kelas' 
    AND c.ta=$ta_aktif 
    AND c.status=1 
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) {
      $link_back = $dari == '' ? '' : "  <a href='?$dari'>Kembali</a>";
      $pesan .= div_alert('danger mt2', "Maaf, data untuk username <span class=darkblue>$username</span> kelas <span class=darkblue>$kelas</span> tidak ditemukan. <hr>Jika Anda juga lupa dengan <i>username</i>, maka silahkan hubungi ke nomor $Trainer Anda.<hr>$link_back | <a href='?'>Homepage</a>");
      $hide_form = 1;
    } else {
      $d = mysqli_fetch_assoc($q);
      $no_wa = $d['no_wa'];
      $nama = $d['nama'];

      if ($no_wa == '') {
        $pesan .= div_alert('info', "Anda belum memasukan Nomor Whatsapp. Silahkan masukan dahulu nomor yang aktif.");
      } else {
        $pesan .= div_alert('info', "Data whatsapp sudah ada, silahkan klik Verifikasi/Reset. Jika nomor aktif saat ini berbeda maka silahkan input kembali.");
      }
    }
  }
}

if ($dari == 'routing_verifikasi_wa_instruktur') {
  $pesan = "Agar dapat lanjut sebagai $sebagai nomor whatsapp Anda harus terverifikasi oleh Master $Trainer. Pesan dan link verifikasi akan diteruskan ke Developer Team (Bapak Iin Sholihin)";

  // $hide_form = 0;
  // $hide_section = 0;
}

if (isset($_POST['btn_submit_wa'])) {





  $dari = $_POST['dari'];

  $username = $_POST['username_reset'];
  $kelas = $_POST['kelas'];
  $no_wa = $_POST['no_wa'];
  $nama = $_POST['nama'];
  if ($_POST['dari'] == 'reset_password') {

    include 'reset_password_confirm.php';
    $hide_section = 1;
  } elseif ($_POST['dari'] == 'routing_verifikasi_wa_instruktur') {
    include 'verifikasi_wa_instruktur_baru.php';
    exit;
  } else {
    // verifikasi normal
    echo "VERIFIKASI NORMAL - Unhandler Code.";
    exit;
  }
} else {
  // $pesan .= div_alert('danger', 'No POST request detected.');

}

if (!$hide_form) {
  $form = "
  <form method=post id=form_verifikasi_wa>
    <input class=debug name=nama value='$nama_peserta'>
    <input class=debug name=dari value='$dari'>
    <input class=debug name=username_reset value='$username'>
    <input class=debug name=kelas value='$kelas'>
    <div class='wadah gradasi-hijau' data-aos='fade-up' data-aos-delay='200'>
      <label for='no_wa' class='tengah mb1'>Nomor WhatsApp <span class='kecil abu miring'>* yang aktif</span></label>
      <input type='text' class='form-control tengah' minlength=11 maxlength=14 id=no_wa autocomplete=off style='color:gray' name=no_wa required value='$no_wa'>
      <div class='tengah consolas f30' id=no_wa2>628X-XXX-XXX-XXX</div>
      <div class='tengah consolas red' style='font-size:10px' id=no_wa_invalid>awali dg '08...' atau '62...'</div>
      <div>
        <button class='btn btn-primary btn-block' id=btn_verifikasi name=btn_submit_wa>$caption</button>
      </div>
    </div>
  </form>
  ";
}

if (!$hide_section) {
?>
  <section>
    <div class="container">

      <div class="section-title" data-aos="fade-up">
        <h2>Verifikasi WA</h2>
        <p><?= $pesan ?></p>
      </div>
      <?= $form ?>
      <?= $debug ?>
    </div>
    </div>
  </section>
<?php } ?>


<script>
  $(function() {
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

    $("#no_wa").inputFilter(function(value) {
      return /^\d*$/.test(value);
    });
    $("#no_wa").keyup(function() {
      let val = $(this).val();
      let val2 = val.substring(0, 4) +
        '-' + val.substring(4, 7) +
        '-' + val.substring(7, 10) +
        '-' + val.substring(10, 14);
      val2 = val2 == '' ? '-' : val2;
      $('#no_wa2').text(val2);


      if (val.substring(0, 2) == '08' || val.substring(0, 2) == '62') {
        $('#no_wa_invalid').text('');
        if (val.substring(0, 2) == '08') {
          val = '628' + val.substring(2, 14);
          $('#no_wa').val(val);
        }
      } else {
        $('#no_wa_invalid').text('awali dg "08..." atau "62..."');
        if (val.length > 2) {
          $(this).val('');
        }
        return;
      }




      if (val.length > 10) {
        $('#btn_verifikasi').fadeIn();
      } else {
        $('#btn_verifikasi').fadeOut();
      }
    });
  })
</script>