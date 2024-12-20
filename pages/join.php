<?php
$img_home = img_icon('home');
set_h2($join_title, "<a href='?'>$img_home</a> <a href='?login'>$img_login_as</a> ");
$as = $_GET['as'] ?? '';
$as = strtolower($as);















# ===========================================================
# PROCESSORS
# ===========================================================
$pesan_join = '';
$nama = '';
$username = '';
$select_kelas = '';
if (isset($_POST['btn_join'])) {

  function clean($a)
  {
    return str_replace('"', '', str_replace('\'', '', $a));
  }

  $nama = clean($_POST['nama']);
  $username = clean($_POST['username']);
  $select_kelas = clean($_POST['select_kelas']);

  $s = "SELECT 1 FROM tb_peserta WHERE username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $pesan_join = "<div class='alert alert-danger' data-aos='fade-left'>Nickname <b><u>$username</u></b> sudah diambil. Silahkan tambahkan nickname Anda dengan angka, nama tengah, atau nama belakang (tanpa spasi atau karakter khusus).</div>";
  } else { // input username sudah unik

    // default status peserta baru = aktif
    $status = 1;
    $id_role = 1; // default as peserta
    if ($as != 'peserta') {
      $status = 0; // perlu verifikasi untuk instruktur, pro, mitra baru
      if ($as == 'instruktur') {
        $id_role = 2;
      } elseif ($as == 'praktisi') {
        $id_role = 3;
      } elseif ($as == 'mitra') {
        $id_role = 4;
      } else {
        die('Undefined role at processors.');
      }
    }

    // add peserta
    $s = "INSERT INTO tb_peserta 
      (username,nama,status,id_role) VALUES 
      ('$username','$nama','$status',$id_role) 
      ON DUPLICATE KEY UPDATE date_created=CURRENT_TIMESTAMP 
      ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Insert $as baru sukses...");

    // get id_peserta
    $s = "SELECT id FROM tb_peserta where username='$username'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $id_peserta = $d['id'];
    echo div_alert('info', 'Getting new id_peserta sukses...');

    // assign kelas peserta
    $s = "INSERT INTO tb_kelas_peserta 
      (id_peserta,kelas) VALUES 
      ('$id_peserta','$select_kelas')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Assign peserta baru ke kelas <u>$select_kelas</u> sukses...");





    echo div_alert('success', "Semua proses join selesai.<hr><span class='tebal darkred'>Mohon tunggu! redirecting...</span>");

    $pesan = div_alert('success', "Join sebagai $as dengan nickname: <b>$username</b> berhasil.<hr><span class='darkblue'>Silahkan Anda login dengan username yang barusan Anda buat.
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
















# ===========================================================
# NORMAL FLOW :: SELECT AS
# ===========================================================
if (!$as) {
  $arr_as = ['peserta', 'instruktur', 'praktisi', 'mitra'];
  $arr_gradasi = ['hijau', 'hijau', 'biru', 'kuning'];
  $arr_ket = [
    'Saya ingin belajar dengan target di dunia nyata. Saya akan mengerjakan Challenges baik dari instruktur maupun dari mitra.',
    'Koordinator mahasiswa, praktisi, dan mitra (industri). Saya mempertemukan para mahasiswa, pihak mitra, dan juga para professional.',
    'Saya bersedia mentoring dengan senang hati. Saya akan membagikan pengalaman saya di dunia kerja bagi adik-adik mahasiswa.',
    'Saya membutuhkan jasa dari mahasiswa. Dimulai dari yang simple saja!'
  ];

  $blok_joins = '';
  foreach ($arr_as as $key => $value) {
    $time_anim = ($key + 1) * 150;
    $value_title = $institusi ? $custom[$value] : $value;
    $blok_joins .= "
    <div class='col-lg-3' data-aos='fade-up' data-aos-delay='$time_anim'>
      <div class='wadah gradasi-$arr_gradasi[$key]'>
        <div class='text-center p-4'>
          <img src='assets/img/icon/$value.png' alt='as $value' class='foto-ilustrasi'>
        </div>
        <a href='?join&as=$value' class='btn btn-primary btn-block proper'>Sebagai $value_title</a>
        <div class='tengah kecil abu mt1'>$arr_ket[$key]</div>
      </div>
    </div>
  ";
  }

  echo "
    <div class='row content'>
      $blok_joins
    </div>

    <div class='tengah mt3' data-aos='fade-up' data-aos-delay='800'>
      Sudah punya akun? Silahkan 
      <a href='?login'><b>Login $img_login_as</b></a>
      <hr> 
      <a href='?'>Back Home $img_home</a>
    </div>
  ";
} else {































  # ===========================================================
  # SELECTED AS
  # ===========================================================
  if ($as == 'peserta') {
    $s = "SELECT kelas FROM tb_kelas WHERE ta=$ta AND status=1  ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $option_kelas = '';
    while ($d = mysqli_fetch_assoc($q)) {
      $selected = $d['kelas'] == $select_kelas ? 'selected' : '';
      $option_kelas .= "<option $selected>$d[kelas]</option>";
    }
  } elseif ($as == 'instruktur') {
    $option_kelas = '<option value="INSTRUKTUR">KELAS INSTRUKTUR</option>';
  } elseif ($as == 'praktisi') {
    $option_kelas = '<option value="PRAKTISI">KELAS PRAKTISI</option>';
  } elseif ($as == 'mitra') {
    $option_kelas = '<option value=MITRA>MITRA INDUSTRI</option>';
  } else {
    die('Undefined role.');
  }

  $hideit_btn_join = ($nama != '' and $username != '' and $select_kelas != '0') ? '' : 'hideit';
  $as_title = $custom[$as] ?? 'Peserta';

  echo "
  <div class='section-title' data-aos='fade-up'>
    <p><a href='?join'>Back</a> | Silahkan Anda $join_title sebagai <span class=proper>$as_title</span></p>
    <div class='mt3 mb4'>
      <img src='assets/img/icon/$as.png' alt='img-as-$as' class='foto-ilustrasi'>
    </div>
    $pesan_join
  </div>
  
  ";

  $input_username = '';
  if (!$file_config_institusi) {
    $input_username = "
          <label for='username'>Username</label>
          <input type='text' required maxlength=20 minlength=3 class='form-control input_isian mt1' id='username' name='username'  value='$username'>
          <div class='f12 miring mt1'>Usahakan agar username adalah nama depan atau nama panggilan!</div>
    ";
  } elseif ($file_config_institusi == 'mu') {
    $input_username = "
      <label for='username'>Username (NIM)</label>
      <input type='text' required maxlength=9 minlength=9 class='form-control input_isian mt1'  id='username' name='username'  value='$username'>
      <div class='f12 miring mt1'>NIM 9 digit angka</div>
    ";
  } else {
    die("File config [ $file_config_institusi ] institusi tidak ditemukan");
  }

  echo "
    <div class='wadah gradasi-hijau' data-aos='fade-up' data-aos-delay='150' style='max-width:500px; margin:auto'>
      <form method=post>
        <div class='form-group'>
          <label for='nama'>Nama Lengkap</label>
          <input class='form-control input_isian mt1' type='text' id='nama' name='nama' required maxlength=50 minlength=3 value='$nama'>
        </div>
        <div class='form-group'>
          $input_username
        </div>
        <div class='form-group'>
          <label for='select_kelas'>Kelas Aktif <span class='f12 abu'>pada TA $ta</span></label>
          <select name='select_kelas' id='select_kelas' class='form-control'>
            <option value='0'>--Pilih--</option>
            $option_kelas
          </select>
          <div class='f12 miring mt1 mb2'>Jika Kelas Aktif belum ada silahkan hubungi intruktur!</div>
        </div>

        <div class='form-group $hideit_btn_join' id='blok_btn_join'>
          <button class='btn btn-primary btn-block' name=btn_join>Join</button>
        </div>
      </form>
    </div>  
  ";
?>



  <!-- <div class="tengah kecil mt3" data-aos="fade-up" data-aos-delay="300">Punya akun? Silahkan <a href="?login">Login</a></div> -->

  <script>
    $(function() {
      $('#select_kelas').change(function() {

        let val = $(this).val();
        if (val == '0') {
          $('#blok_new_kelas').hide();
          $('#kelas_new').val('null');
          $('#blok_btn_join').fadeOut();
        } else {
          $('#blok_btn_join').fadeIn();
          $('#kelas_new').val('');
          if (val == 'new') {
            $('#blok_new_kelas').show();
            console.log(val, $('#blok_new_kelas').val());
          } else {
            $('#blok_new_kelas').hide(); //pilih kelas yg sdh ada
            $('.input_isian').keyup();
            $('#kelas_new').val('null');
          }
        }
      })

      $('#username').keyup(function() {
        $(this).val(
          $(this).val()
          .trim()
          .toLowerCase()
          .replace(/ /g, '')
          .replace(/[!@#$%^&*()+\-=\[\]{}.,;:'`"\\|<>\/?~]/gim, '')
        );

      });

      $('#nama').keyup(function() {
        $(this).val(
          $(this).val()
          .replace(/['"]/g, '`')
          .replace(/[!@#$%^&*()+\-_=\[\]{}.,;:\\|<>\/?~0-9]/gim, '')
          .replace(/  /g, ' ')
          .replace(
            /\w\S*/g,
            function(txt) {
              return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            }
          )
        );

      });

      $('#nama').focusout(function() {
        $(this).val(
          $(this).val()
          .trim()
        );
      });

      $('.input_isian').keyup(function() {
        let link_wa = 'https://api.whatsapp.com/send?phone=6287729007318&text=*Verification Link Request*%0a%0a';
        let nama = $('#nama').val();
        let username = $('#username').val();
        let kelas = $('#select_kelas').val() == 'new' ? $('#kelas_new').val() : $('#select_kelas').val();

        let href = `${link_wa}&nama=${nama}&username=${username}&kelas=${kelas}`;

        $('#link_btn_join').prop('href', href);
      })
    })
  </script>

<?php } ?>