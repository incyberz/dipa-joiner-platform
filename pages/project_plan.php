<link rel="stylesheet" href="../assets/css/form.css">
<?php
if (isset($_POST['judul_sistem'])) {

  // anti SQL injection
  foreach ($_POST as $k => $v) {
    if (is_array($v)) {
      foreach ($v as $k2 => $v2) {
        $_POST[$k][$k2] = str_replace(';', ',', str_replace('\'', '`', strip_tags($v2)));
      }
    } else {
      $_POST[$k] = str_replace(';', ',', str_replace('\'', '`', strip_tags($v)));
    }
  }

  $json_data = json_encode($_POST);

  $s = "INSERT INTO tb_project_plan (
    id,
    id_peserta,
    id_room,
    json_data
  ) VALUES (
    '$id_peserta-$id_room',
    $id_peserta,
    $id_room,
    '$json_data'
  ) ON DUPLICATE KEY UPDATE 
    json_data = '$json_data'
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo 'INSERT sukses.';
  jsurl();
}


# ============================================================
# SELECT DB
# ============================================================
$s = "SELECT json_data FROM tb_project_plan WHERE id_peserta=$id_peserta AND id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
if ($d) {
  $json_data = json_decode($d['json_data'], true);
  $ada_data = 1;
} else {
  $json_data = [];
  $ada_data = '';
}
echo "<i id=ada_data class=hideit>$ada_data</i>";


# ============================================================
# MAIN 
# ============================================================
$user = [];
$user['eksternal'] = '';
$user['internal'] = '';
$rusers = [
  'eksternal' => [
    1 => [
      'placeholder' => 'Contoh: Pengunjung (Calon Pelanggan)',
      'access' => 'Akses: Pilih Produk, CRUD Keranjang, Daftar Member',
    ],
    2 => [
      'placeholder' => 'Contoh: Member (Pengunjung yang Terdaftar)',
      'access' => 'Akses: Cekout, Metode Bayar, Pelacakan Transaksi',
    ],
    3 => [
      'placeholder' => 'Contoh: Supplier',
      'access' => 'Akses: Purchase Order, Stok Publik',
    ],
    4 => [
      'placeholder' => 'Contoh: Owner',
      'access' => 'Akses: Monthly Report',
    ],
    5 => [
      'placeholder' => 'Contoh: Investors',
      'access' => 'Akses: Investment Report',
    ],
  ],
  'internal' => [
    1 => [
      'placeholder' => 'Contoh: Admin System (Kepala Keuangan)',
      'access' => 'Akses: CRUD Metode Bayar, Diskon, Approval PO, Retur PO',
    ],
    2 => [
      'placeholder' => 'Contoh: Keuangan (Staf Keuangan)',
      'access' => 'Akses: CRUD Produk, Manage Display, Cek Pembayaran Non-Cash',
    ],
    3 => [
      'placeholder' => 'Contoh: Kasir',
      'access' => 'Akses: Transaksi Penjualan Cash/Card',
    ],
    4 => [
      'placeholder' => 'Contoh: Pramuniaga, Staf Gudang',
      'access' => 'Akses: Update Real Stok Barang, Lapor Barang Hilang, Retur Kadaluarsa',
    ],
    5 => [
      'placeholder' => 'Contoh: Manajer (Kepala Gudang)',
      'access' => 'Akses: Pengajuan Purchase Order, Penghangusan',
    ],
  ],
];
foreach ($rusers as $tipe => $rv) {
  $i = 0;
  foreach ($rv as $no => $v) {
    $i++;
    // echo '<pre>';
    // print_r($json_data["user_$tipe--"][$no]);
    // echo '<b style=color:red>Developer SEDANG DEBUGING: exit(true)</b></pre>';
    // exit;
    $value_user = $json_data["user_$tipe--"][$no] ?? '';
    $value_access = $json_data["access_user_$tipe--"][$no] ?? '';
    $user[$tipe] .= "
      <div class='d-flex gap-2 mb-3'>
        <div>$no</div>
        <div class=flex-fill>
          <div class='row'>
            <div class='col-lg-4'>
              <input
                value='$value_user'
                id=user_$tipe--$i
                name=user_$tipe--[$i]
                class='form-control form-control-sm mb-2'
                placeholder='$v[placeholder]'
                required />
            </div>
            <div class='col-lg-8'>
              <input
                value='$value_access'
                id=access_user_$tipe--$i
                name=access_user_$tipe--[$i]
                class='form-control form-control-sm mb-2'
                placeholder='$v[access]'
                required />
            </div>
          </div>
    
        </div>
      </div>
    ";
  }
}














# ============================================================
# FITURS
# ============================================================
$fiturs = '';
$rfiturs = [
  'basic' => 'Fitur yang wajib ada',
  'tambahan' => 'Fitur custom (request users) untuk melengkapi fitur utama',
  'advanced' => 'Fitur yang menggunakan Logika AI, IOT, REST-API, Cloud-DB, Realtime Updater, GPS, atau advanced web technology lainnya',
];
foreach ($rfiturs as $fitur => $ket) {
  $inputs = '';
  for ($i = 1; $i <= 5; $i++) {
    $value = $json_data["fitur_$fitur--"][$i] ?? '';

    $inputs .= "
      <input
        value='$value'
        id=fitur_$fitur--$i
        name=fitur_$fitur--[$i]
        class='form-control form-control-sm mb-2'
        placeholder='Fitur $fitur $i'
        required 
      />
    ";
  }
  $fiturs .= "
    <div class='col-lg-4'>
      <div class='card'>
        <div class='card-header bg-info text-white proper'>Fitur $fitur</div>
        <div class='card-body'>
          <div class='f12 mb-2'>$ket</div>
          $inputs
        </div>
      </div>
    </div>
  ";
}

















# ============================================================
# STACKS 
# ============================================================
$stacks = [];
$stacks['PHP'] = '';
$stacks['JavaScript'] = '';
$rstacks = [
  'PHP' => [
    'bg' => 'primary',
    'placeholder_lainnya' => 'Contoh: Composer, PHPMailer',
    'techs' => [
      'PHP',
      'MySQL',
      'Bootstrap',
      'JQuery',
      'Laravel',
      'CodeIgniter',
    ],
  ],
  'JavaScript' => [
    'bg' => 'success',
    'placeholder_lainnya' => 'Contoh: Express.js, Axios',
    'techs' => [
      'Node JS',
      'Next JS',
      'REST-API',
      'Framer Motion',
      'Firebase',
      'Mongo DB',
    ],
  ]
];
foreach ($rstacks as $stack => $rv) {
  $techs = '';
  // $json_data["fitur_$fitur--"]
  foreach ($rv['techs'] as $key => $tech) {
    $id = strtolower(str_replace(' ', '_', $tech));
    $checked = isset($json_data["stack--$id"]) ? 'checked' : '';
    $disabled = (isset($json_data['radio-stack']) and $json_data['radio-stack'] == $stack) ? '' : 'disabled';
    $techs .= "
      <div class='form-check'>
        <input
          class='form-check-input input-stack stack--$stack'
          type='checkbox'
          id='stack--$id' 
          name='stack--$id' 
          $disabled
          $checked
        />
        <label class='form-check-label' for='stack--$id'>$tech</label>
      </div>
    ";
  }

  $value_lainnya = $json_data["tech-lainnya--$stack"] ?? '';
  $checked = (isset($json_data['radio-stack']) and $json_data['radio-stack'] == $stack) ? 'checked' : '';

  $stacks[$stack] .= "
    <div class='col-md-6'>
      <div class='card'>
        <div class='card-header bg-$rv[bg] text-white'>
          <label>
            <input 
              required 
              class='radio-stack' 
              type=radio 
              name=radio-stack 
              id=tech-stack--$stack 
              value=$stack $checked
            />
            Stack $stack
          </label>
        </div>
        <div class='card-body'>
          $techs

          <div class='mb-3 mt-2'>
            <label for='others--$stack' class='form-label'>Library $stack lainnya</label>
            <input
              type='text'
              class='form-control input-stack stack--$stack'
              id='others--$stack'
              name=tech-lainnya--$stack
              value='$value_lainnya'
              placeholder='$rv[placeholder_lainnya]' 
              $disabled
              />
          </div>

        </div>
      </div>
    </div>      
  ";
}















# ============================================================
# LOGIN SYSTEMS
# ============================================================
$rlogins = [
  'basic' => [
    'title' => 'Cek Username dan Password',
    'required' => 'required',
  ],
  'register' => [
    'title' => 'Register User Baru',
    'required' => 'required',
  ],
  'verif-username' => ['title' => 'Verifikasi Username (availabilitas, length, dan character-case)',],
  'verif-whatsapp' => ['title' => 'Verifikasi Whatsapp (availabilitas, length, dan approval-by-admin)',],
  'verif-email' => ['title' => 'Verifikasi Email (availabilitas dan auto-approval-by Mail Server)',],
  'capthcha' => ['title' => 'Capthcha (Pencegah Brute Force Attack)',],
  'sign-in-by-goole' => ['title' => 'Sign in by Google (tidak perlu input username dan password)',],
];

$logins = '';
foreach ($rlogins as $k => $rv) {
  $required = $rv['required'] ?? '';
  $json_data["login-system--$k"] = $json_data["login-system--$k"] ?? null;
  $checked = $json_data["login-system--$k"] ? 'checked' : '';
  $logins .= "
    <li><label class='d-flex gap-2 label-$required'>
      <div><input 
        id=login-system--$k 
        name=login-system--$k 
        value=1 
        type='checkbox' 
        $required 
        $checked
      /></div>
      <div>$rv[title]</div>
    </label></li>";
}



















?>

<h2 class="mb-4 text-center">
  📝 Formulir Project Plan<br /><small class="text-muted">Mata Kuliah <?= $nama_room ?></small>
</h2>

<?php
if ($id_role == 2) echo "<div class='my-3'><a class='btn btn-success w-100' href=?project_plan_monitoring>Project Plan Monitoring</a></div>";
?>

<form method=post>
  <!-- Identitas -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label class="form-label">Nama Mahasiswa</label>
      <input type="text" class="form-control" disabled value="<?= $nama_peserta ?>" />
    </div>
    <div class="col-md-6">
      <label class="form-label">Kelas</label>
      <input type="text" class="form-control" disabled value="<?= $kelas ?>" />
    </div>
  </div>



  <hr>
  <!-- Judul Proyek -->
  <div class="mb-3">
    <label for="judul" class="form-label">1. Judul Proyek Aplikasi Web</label>
    <input type="text" class="form-control" id="judul" required placeholder="Contoh: Sistem Informasi e-Commerce Ma`soem Online Supermart Cipacing" name=judul_sistem value="<?= $json_data['judul_sistem'] ?? '' ?>" />
  </div>

  <!-- Deskripsi -->
  <div class="mb-3">
    <label for="deskripsi" class="form-label">2. Deskripsi Singkat Aplikasi</label>
    <textarea
      name=deskripsi
      class="form-control"
      id="deskripsi"
      rows="3"
      required
      placeholder="Contoh: Proyek Pembuatan Sistem Informasi e-Commerce untuk Ma`soem Online Supermart Cipacing. Dipakai oleh seluruh users via online dan multi-platform. Users terdiri dari Admin System (kepala keuangan), staf gudang, kepala gudang, pramuniaga, kasir, staf keuangan, hingga manajer umum"><?= $json_data['deskripsi'] ?? '' ?></textarea>
  </div>

  <!-- Target Pengguna -->
  <div class="mb-3">
    <label for="pengguna" class="form-label">3. Target Pengguna</label>
    <div class="card">
      <div class="card-header bg-info text-white">
        User Eksternal dan Hak Akses nya
      </div>
      <div class="card-body">
        <div class="f12 mb-2">*) User Eksternal adalah yang tidak menjadi bagian dari aktifitas di perusahaan, masukan strip jika tidak ada</div>
        <?= $user['eksternal'] ?>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header bg-info text-white">User Internal</div>
    <div class="card-body">
      <div class="f12 mb-2">*) User Internal adalah yang bekerja di perusahaan (Business Worker), Admin System wajib 1 orang, role lain multiple, masukan strip jika tidak ada</div>
      <?= $user['internal'] ?>
    </div>
  </div>

  <!-- Fitur Utama -->
  <div class="mb-3">
    <label class="form-label">4. Fitur (Sub-Program) yang Akan Dikembangkan</label>
    <div class="row">
      <?= $fiturs ?>
    </div>
  </div>

  <!-- Teknologi -->
  <div class="mb-4">
    <h5>5. Teknologi dan Platform yang Digunakan</h5>
    <div class="row">
      <?= $stacks['PHP'] ?>
      <?= $stacks['JavaScript'] ?>
    </div>
    <div class="my-3">
      Target Platform:
      <ul>
        <li>
          <label>
            <input id=platform--web-desktop name=platform--web-desktop value=web-desktop type="checkbox" required <?= (isset($json_data['platform--web-desktop']) and $json_data['platform--web-desktop']) ? 'checked' : '' ?>>
            <b>Web Desktop</b>: untuk Admin System dan users yang memakai laptop
          </label>
        </li>
        <li>
          <label>
            <input id=platform--web-mobile name=platform--web-mobile value=web-mobile type="checkbox" required
              <?= (isset($json_data['platform--web-mobile']) and $json_data['platform--web-mobile']) ? 'checked' : '' ?>>
            <b>Web Mobile</b>: untuk users yang memakai mobile gadget via browser (Mobile Responsive)
          </label>
        </li>
        <li>
          <label>
            <input id=platform--mobile-view-app name=platform--mobile-view-app value=mobile-view-app type="checkbox" <?= (isset($json_data['platform--mobile-view-app']) and $json_data['platform--mobile-view-app']) ? 'checked' : '' ?>>
            <b>Mobile View App</b>: untuk users via Playstore/Appstore, konversi Website ke APK
          </label>
        </li>
        <li>
          <label>
            <input id=platform--mobile-app name=platform--mobile-app value=mobile-app type="checkbox" disabled>
            <b>Mobile App (Native)</b>: memakai bahasa khusus Mobile Native App semisal Flutter (sementara not available di MK Pa Iin)
          </label>
        </li>
      </ul>
    </div>
  </div>

  <!-- Struktur Folder -->
  <div class="mb-3">
    <label for="struktur" class="form-label">6. Login System (Authentication) dan Roles (Authorization)</label>
    <ul class="my-3">
      <li><b>Authentication</b>: Proses login agar user dapat mengakses dashboard/menu</li>
      <li><b>Authorization</b>: Proses penentuan fitur-fitur mana yang boleh diakses via dashboard/menu</li>
      <li><b>Contoh</b>: Setelah Kasir berhasil login (Authentication), system melakukan Authorization dg hanya mengizinkan fitur Transaksi Penjualan (Aplikasi Kasir) saja yang boleh diakses, fitur lainnya disembunyikan, dan tampilkan error jika user memaksa mengakses fitur yang tidak sesuai otorisasi-nya</li>
      <li><b>Bentuk</b>: proses Authentication diwakili oleh <b class="darkblue">Login System</b> dan verifikasinya, sedangkan Authorization dalam bentuk <b class=darkblue>Tabel Authorization</b> yang disetujui oleh semua users (Roles) diawal development</li>
      <li>Login System pada project saya akan dilengkapi dengan:
        <ul class="my-2" style="list-style: none;padding-left:10px">
          <?= $logins ?>
        </ul>
      </li>
      <li>Untuk <i class="consolas">Authorization Tabel</i> sudah saya buat. Upload dalam format file Ms Word:
        <div class="my-3">
          <input type="file" class="form-control" disabled>
          <div class="f12 mt-2">)* akan diminta pada Latihan di Learning Path tertentu.</div>
        </div>
      </li>
    </ul>

  </div>

  <!-- Target Progress Mingguan -->
  <div class="mb-4">
    <label class="form-label fw-bold">7. Target Progress Mingguan (P10 - P14)</label>
    <p class="text-muted mb-3">
      Jelaskan aktivitas atau fitur yang akan Anda selesaikan di setiap
      minggu.
    </p>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="">
          <tr>
            <th>Sesi</th>
            <th>Rencana Aktivitas / Fitur yang akan diselesaikan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>P10</td>
            <td>
              <input
                value='<?= $json_data['plan-in-p10'] ?? '' ?>'
                name=plan-in-p10
                id=plan-in-p10
                required
                type="text"
                class="form-control"
                placeholder="Contoh: Membuat struktur folder, setup project awal, login page" />
            </td>
          </tr>
          <tr>
            <td>P11</td>
            <td>
              <input
                value='<?= $json_data['plan-in-p11'] ?? '' ?>'
                name=plan-in-p11
                id=plan-in-p11
                required
                type="text"
                class="form-control"
                placeholder="Contoh: Halaman dashboard, fitur input data utama" />
            </td>
          </tr>
          <tr>
            <td>P12</td>
            <td>
              <input
                value='<?= $json_data['plan-in-p12'] ?? '' ?>'
                name=plan-in-p12
                id=plan-in-p12
                required
                type="text"
                class="form-control"
                placeholder="Contoh: CRUD data, validasi form, koneksi database" />
            </td>
          </tr>
          <tr>
            <td>P13</td>
            <td>
              <input
                value='<?= $json_data['plan-in-p13'] ?? '' ?>'
                name=plan-in-p13
                id=plan-in-p13
                required
                type="text"
                class="form-control"
                placeholder="Contoh: Integrasi API, upload file, tampilan responsive" />
            </td>
          </tr>
          <tr>
            <td>P14</td>
            <td>
              <input
                value='<?= $json_data['plan-in-p14'] ?? '' ?>'
                name=plan-in-p14
                id=plan-in-p14
                required
                type="text"
                class="form-control"
                placeholder="Contoh: Hosting ke 000webhost/vercel, testing akhir, demo aplikasi" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- GitHub & Hosting -->
  <div class="mb-3">
    <label for="github" class="form-label">8. Rencana Nama Repository dan Link GitHub</label>
    <input
      value='<?= $json_data['link-github'] ?? '' ?>'
      name=link-github
      required
      type="url"
      class="form-control"
      id="github"
      placeholder="Contoh: https://github.com/incyberz/e-commerce-2025" />
  </div>

  <div class="mb-3">
    <label for="hosting" class="form-label">9. Rencana Penamaan Link Hosting</label>
    <input
      value='<?= $json_data['link-hosting'] ?? '' ?>'
      name=link-hosting
      required
      type="url"
      class="form-control"
      id="hosting"
      placeholder="Contoh: https://namakamu.000webhostapp.com" />
  </div>

  <!-- Kendala -->
  <div class="mb-4">
    <label for="kendala" class="form-label">10. Catatan atau Kendala yang Diperkirakan</label>
    <textarea
      required
      name=catatan-kendala
      class="form-control"
      id="kendala"
      rows="3"
      placeholder="Contoh: kesulitan Login via Google, Cloud-Database, Integrasi API, dsb."><?= $json_data['catatan-kendala'] ?? '' ?></textarea>
  </div>

  <!-- Tombol Submit -->
  <div class="text-center p-3 border-top" style="position: fixed; inset: auto 0 0 0; background: black; z-index:999">
    <div class="progress">
      <div class='progress-bar bg-primary progress-bar-animated' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'></div>
    </div>
    <button type="submit" class="btn btn-success mt-3 px-3" id=btn_submit>Submit My Project Plan</button>
  </div>
</form>
<script>
  function hitung_terisi() {
    let total = $('input:required').length +
      $('textarea:required').length +
      $('select:required').length;
    let valid = $('input:required:valid').length +
      $('textarea:required:valid').length +
      $('select:required:valid').length;
    let persen = Math.round(valid * 100 / total);
    $('.progress-bar').prop('style', `width:${persen}%`)
    if (valid == total) {
      let Submit = $('#ada_data').text() == '1' ? 'Update' : 'Submit'
      $('#btn_submit').text(`Pengisian ${persen}% - ${Submit} Project Plan`)

    } else {
      $('#btn_submit').text(`Yang wajib Anda isi: ${valid} of ${total} (${persen}%)`)

    }
  }
  $(function() {
    hitung_terisi();
    $('.radio-stack').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('--');
      let aksi = rid[0];
      let stack = rid[1];
      $('.input-stack').prop('disabled', 1);
      $('.input-stack').prop('checked', 0);
      $('.input-stack').val('');
      $('.stack--' + stack).prop('disabled', 0);
    });

    $("input:required").change(function() {
      hitung_terisi();
    })
    $("textarea:required").change(function() {
      hitung_terisi();
    })
    $("select:required").change(function() {
      hitung_terisi();
    })

    // ===========================================
    // SET COOKIE
    // ===========================================
    $("input,textarea,select").focusout(function() {
      document.cookie = `${$(this).prop('id')}=${$(this).val().replace('=', '--').replace('|', '/').replace(';', ',')}; max-age=3600`;
    });

    // ===========================================
    // AUTOFILL COOKIE
    // ===========================================
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
    }

    $("input, textarea, select").each(function() {
      const id = this.id;
      const type = this.type;
      const tag = this.tagName.toLowerCase();

      if (!id) return;

      const cookieValue = getCookie(id);
      if (cookieValue === undefined) return;

      if (type === 'checkbox') {
        $(this).prop('checked', cookieValue);
      } else if (type === 'radio') {
        // Radio buttons biasanya punya name yang sama, bukan id
        if (this.value === cookieValue) {
          $(this).prop('checked', true);
        }
      } else {
        $(this).val(cookieValue);
      }
    });



  })
</script>