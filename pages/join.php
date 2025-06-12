<link rel="stylesheet" href="./assets/css/form.css">
<script src="./assets/js/form.js"></script>
<?php
$img_home = img_icon('home');
set_h2($Join, "<a href='?'>$img_home</a> <a href='?login'>$img_login_as</a> ");
$as = $_GET['as'] ?? '';
$as = strtolower($as);
$emoji = [
  'nama' => '👤',
  'whatsapp' => '📞',
  'username' => '🆔',

  'nim' => '🆔',
  'nidn' => '🆔',
  'kelas' => '🆔',
  'bidang_ilmu' => '💼',

  'bidang_profesi' => '💼',
  'nama_instansi' => '🏢',
  'instansi' => '🏢',
  'alamat_instansi' => '📍',

  'bidang_usaha' => '💼',
  'usaha' => '🏢',
  'nama_usaha' => '🏢',
  'alamat_usaha' => '📍',
];
















# ===========================================================
# PROCESSORS
# ===========================================================
$pesan_login_error = '';
include 'join-process.php';

# ===========================================================
# GLOBAL VAR
# ===========================================================
$nama = $_POST['nama'] ?? '';
$username = $_POST['username'] ?? '';
$select_kelas = $_POST['select_kelas'] ?? '';
$whatsapp = $_POST['whatsapp'] ?? '';

$nim = $_POST['nim'] ?? ''; // mhs

$nidn = $_POST['nidn'] ?? ''; // dosen
$bidang_ilmu = $_POST['bidang_ilmu'] ?? '';

$bidang_profesi = $_POST['bidang_profesi'] ?? ''; // praktisi
$nama_instansi = $_POST['nama_instansi'] ?? '';
$alamat_instansi = $_POST['alamat_instansi'] ?? '';

$nama_usaha = $_POST['nama_usaha'] ?? ''; // industri
$bidang_usaha = $_POST['bidang_usaha'] ?? '';
$alamat_usaha = $_POST['alamat_usaha'] ?? '';















# ===========================================================
# TAMPILAN AWAL | AS UNDEFINED
# ===========================================================
if (!$as) {
  $arr_as = ['peserta', 'instruktur', 'praktisi', 'mitra'];
  $arr_As = [$Peserta, $Trainer, $Praktisi, $Mitra];
  $arr_gradasi = ['hijau', 'hijau', 'biru', 'kuning'];
  $arr_peran = [
    'peserta' => "Saya ingin belajar dengan target di dunia nyata. Saya akan mengerjakan Challenges baik dari $Trainer maupun dari mitra.",
    'instruktur' => 'Koordinator mahasiswa, praktisi, dan mitra (industri). Saya mempertemukan para mahasiswa, pihak mitra, dan juga para professional.',
    'praktisi' => 'Saya bersedia mentoring dengan senang hati. Saya akan membagikan pengalaman saya di dunia kerja bagi adik-adik mahasiswa.',
    'mitra' => 'Saya membutuhkan jasa dari mahasiswa. Dimulai dari yang simple saja!'
  ];

  $blok_joins = '';
  foreach ($arr_as as $key => $value) {
    $time_anim = ($key + 1) * 150;
    $value_title = $Institusi ? $arr_As[$key] : $value;
    $peran = $custom_arr_peran[$arr_as[$key]] ?? $arr_peran[$arr_as[$key]];
    $blok_joins .= "
    <div class='col-lg-3' data-aos='fade-up' data-aos-delay='$time_anim'>
      <div class='wadah gradasi-$arr_gradasi[$key]'>
        <div class='text-center p-4'>
          <img src='assets/img/icon/$value.png' alt='as $value' class='foto-ilustrasi'>
        </div>
        <a href='?join&as=$value' class='btn btn-primary btn-block proper'>Sebagai $value_title</a>
        <div class='tengah kecil abu mt1'>$peran</div>
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
  # AS DEFINED
  # ===========================================================
  $As = ucwords($as);

  $option_kelas = '';
  if ($as == 'peserta') {
    $s = "SELECT kelas FROM tb_kelas WHERE ta=$ta_aktif AND status=1  ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $option_kelas = "<option value=''>--Pilih--</option>";
    while ($d = mysqli_fetch_assoc($q)) {
      $selected = $d['kelas'] == $select_kelas ? 'selected' : '';
      $option_kelas .= "<option $selected>$d[kelas]</option>";
    }
  }

  $rwajib = [
    [
      'label' => "$emoji[nama] Nama Lengkap",
      'id' => 'nama',
      'required' => 'required',
      'minlength' => 3,
      'maxlength' => 30,
      'value' => $nama,
      'info' => 'hanya A-Z, 3 s.d 30 karakter',
    ],
    [
      'label' => "$emoji[whatsapp] Whatsapp Aktif",
      'id' => 'whatsapp',
      'required' => 'required',
      'minlength' => 11,
      'maxlength' => 14,
      'value' => $whatsapp,
      'info' => 'Semua aktifitas dan informasi penting akan dikirimkan via whatsapp. Masukanlah Nomor Whatsapp Anda untuk mempermudah verifikasi dan validitas akun! 11 s.d 14 karakter.',
    ],
    [
      'label' => "$emoji[username] Username",
      'id' => 'username',
      'required' => 'required',
      'minlength' => 3,
      'maxlength' => 20,
      'value' => $username,
      'info' => 'Saran username adalah nama panggilan Anda, 3 s.d 20 karakter',
    ],
  ];


  $rtambahan = [
    'peserta' => [
      [
        'label' => "$emoji[nim] Nomor Induk Siswa/Mahasiswa",
        'id' => 'nim',
        'required' => 'required',
        'minlength' => 8,
        'maxlength' => 10,
        'value' => $nim,
        'info' => '8 s.d 10 karakter',
      ],
      [
        'label' => "$emoji[nim] Kelas Aktif <span class='f12 abu'>pada TA $ta_aktif</span>",
        'type' => 'select',
        'id' => 'select_kelas',
        'options' => $option_kelas,
        'required' => 'required',
        'info' => 'Jika Kelas Aktif belum ada silahkan hubungi Game Master!',
      ],
    ],

    'instruktur' => [
      [
        'label' => "$emoji[bidang_ilmu] Bidang Keilmuan",
        'id' => 'bidang_ilmu',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $bidang_ilmu,
        'placeholder' => 'Contoh: Informatika',
      ],
    ],

    'praktisi' => [
      [
        'label' => "$emoji[bidang_profesi] Bidang Profesi",
        'id' => 'bidang_profesi',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $bidang_profesi,
        'placeholder' => 'Contoh: Dokter, Marketing',
      ],
      [
        'label' => "$emoji[nama_instansi] Instansi",
        'id' => 'nama_instansi',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $nama_instansi,
        'placeholder' => 'Contoh: Klinik Mutiara',
      ],
      [
        'label' => "$emoji[alamat_instansi] Alamat Instansi",
        'id' => 'alamat_instansi',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $alamat_instansi,
        'placeholder' => 'Alamat Instansi Anda...',
      ],
    ],

    'mitra' => [
      [
        'label' => "$emoji[bidang_usaha] Bidang Usaha",
        'id' => 'bidang_usaha',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $bidang_usaha,
        'placeholder' => 'Contoh: Makanan, Pakaian',
      ],
      [
        'label' => "$emoji[nama_usaha] Nama Usaha",
        'id' => 'nama_usaha',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $nama_usaha,
        'placeholder' => 'Contoh: Agen Teh Tarik Hanaang',
      ],
      [
        'label' => "$emoji[alamat_usaha] Alamat Usaha",
        'id' => 'alamat_usaha',
        'required' => 'required',
        'minlength' => 5,
        'maxlength' => 30,
        'value' => $alamat_usaha,
        'placeholder' => 'Alamat Usaha Anda...',
      ],
    ],
  ];


  if (!isset($rtambahan[$as])) stop('Undefined role.');

  $rinputs = array_merge($rwajib, $rtambahan[$as]);


  $inputs = '';
  foreach ($rinputs as $key => $field) {
    $field_id = $field['id'] ?? kosong('id');
    $type = $field['type'] ?? 'text';
    $field_info = $field['info'] ?? '';
    $field_name = $field['name'] ?? $field_id;
    $required = $field['required'] ?? '';
    $minlength = $field['minlength'] ?? '';
    $maxlength = $field['maxlength'] ?? '';
    $placeholder = $field['placeholder'] ?? '';
    $value = $field['value'] ?? '';

    $input_info = "<div class='input-info' id='$field_id--info'>$field_info</div>";
    if ($field_id == 'username') $input_info .= "<div class='f12 red mt1' id=username--available><span class='abu'>username available: <i>unchecked...</i></span></div>";

    if ($type == 'select') {
      $input = "
        <select 
          name='$field_name' 
          id='$field_id' 
          class='form-control my-1' 
          required
        >
          $field[options]
        </select>
      ";
    } else {

      $input = "
        <input 
          $required 
          type='$type' 
          class='form-control my-1' 
          id='$field_id' 
          name='$field_name' 
          value='$value' 
          minlength='$minlength' 
          maxlength='$maxlength' 
          placeholder='$placeholder' 
        />
      ";
    }
    $inputs .= "
      <div class='form-group my-3'>
        <label for='$field_id'>$field[label]</label>
        $input
        $input_info
      </div>
    ";
  }

  # ============================================================
  # FINAL ECHO
  # ============================================================
  echo "
    <div class='section-title' data-aos='fade-up'>
      <p><a href='?join'>Back</a> | Silahkan Anda $Join sebagai $As</p>
      <div class='mt3 mb4'>
        <img src='assets/img/icon/$as.png' alt='img-as-$as' class='foto-ilustrasi'>
      </div>
      $pesan_login_error
    </div>
    
    <div class='wadah gradasi-hijau' data-aos='fade-up' data-aos-delay='150' style='max-width:500px; margin:auto'>
      <form method=post id=form-join>

        $inputs

        <div class='form-group mt-3' id='blok_btn_join'>
          <button class='btn btn-primary btn-block' name=btn_join>Join as $As</button>
        </div>
      </form>
    </div>  

    <div class='tengah kecil mt3' data-aos='fade-up' data-aos-delay='300'>Punya akun? <a href='?login'>Enter War</a></div>
  ";































?>
  <script>
    $(function() {

      $('#form-join').on('submit', function(e) {
        let nama = $('#nama').val().trim();
        let whatsapp = $('#whatsapp').val().trim();
        let username = $('#username').val().trim();
        let pesan = null;
        let at = null;
        if (nama.length < 3 || nama.length > 30) {
          pesan = 'Nama antara 3 s.d 30 karakter';
          at = 'nama';
        } else if (whatsapp.length < 11 || whatsapp.length > 14) {
          pesan = 'Whatsapp antara 11 s.d 14 angka';
          at = 'whatsapp';
        } else if (username.length < 3 || username.length > 20) {
          pesan = 'Username antara 3 s.d 20 karakter';
          at = 'username';
        }
        if (pesan) {
          alert(pesan);
          e.preventDefault();
          $('#' + at).focus();
          return false;
        }
        // submit form lanjut...
      });

    })
  </script>

<?php } ?>