<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$id_paket = $_GET['id_paket'] ?? '';
if (intval($id_paket) < 1 and $id_paket !== '') die('Invalid nilai id_paket');
$judul = $id_paket ? 'Edit Paket Soal' : 'Add Paket Soal';
$mode = $id_paket ? 'update' : 'add';
$pesan = $id_paket ? 'Mengubah Data Paket akan mempengaruhi informasi untuk seluruh peserta ujian' : 'Anda sedang melakukan Penambahan Paket Soal untuk ujian baru' . $sebagai;
set_h2($judul, "$pesan<div class=mt2><a href='?manage_paket_soal' >$img_prev</a></div>");

// $global_akhir_ujian = date('Y-m-d H:i', strtotime('now') + 60 * 60);
$global_akhir_ujian = '';

# ============================================================
# SELECT ID SESI
# ============================================================
$opt = '';
$s = "SELECT * FROM tb_sesi WHERE id_room=$id_room ORDER BY no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$no_sesi = 0;
while ($d = mysqli_fetch_assoc($q)) {
  // $id=$d['id'];
  if ($d['jenis'] == 1) {
    $no_sesi++;
    $nama = "P$no_sesi $d[nama]";
  } else {
    $nama = $d['nama'];
  }
  $awal = date('d-M-y', strtotime($d['awal_presensi']));
  $opt .= "<option value=$d[id]>Untuk Sesi $nama (awal pekan: $awal)</option>";
}
$select_id_sesi = "<select class='form-control mb2' name=id_sesi>$opt</select>";
























# ================================================ -->
# PROCESSOR
# ================================================ -->
if (isset($_POST['btn_simpan_paket_soal'])) {
  // clean SQL
  foreach ($_POST as $key => $value) {
    if (is_array($value)) continue;
    $_POST[$key] = clean_sql($value);
  }


  # =============================================
  # VAR OR NULL HANDLER
  # =============================================
  $mode = $_POST['id_paket'] ? 'update' : 'add';
  $id_paket = $_GET['id_paket'] ?? 'NULL';
  if ($mode == 'add') {
    // get auto_increment
    $s = "SELECT MAX(id) AS id_paket FROM tb_paket";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $id_paket = $d['id_paket'] + 1;
    echo div_alert('info', "Menambahkan Paket Soal baru [ADD Mode, id_paket: $id_paket]");
  }
  $id_paket_or_new = $_POST['id_paket'] ? $_POST['id_paket'] : $id_paket;
  // echo div_alert('info', "Menambahkan Paket Soal baru [ADD Mode, id_paket_or_new: $id_paket_or_new]");
  $kisi_kisi_or_null = $_POST['kisi_kisi'] ? "'$_POST[kisi_kisi]'" : 'NULL';



  # =============================================
  # NEW GLOBAL AKHIR UJIAN
  # =============================================
  foreach ($_POST['arr_kelas'] as $kelas) {
    $is_check = $_POST['untuk_kelas'][$kelas] ?? '';
    $paket_kelas = $id_paket . '__' . $kelas;
    $awal_ujian = $kelas == 'INSTRUKTUR' ? 'CURRENT_TIMESTAMP' : $_POST['tanggal_ujian'][$kelas] . ' ' . $_POST['jam_ujian'][$kelas];
    if ($is_check || $kelas == 'INSTRUKTUR') {
      // set global_akhir_ujian
      $akhir_ujian = akhir_ujian($awal_ujian, $_POST['durasi_ujian']);
      if (strtotime($akhir_ujian) > strtotime($global_akhir_ujian)) $global_akhir_ujian = $akhir_ujian;
    }
  } // foreach kelas




  # =============================================
  # TANGGAL PEMBAHASAN HANDLER
  # =============================================
  if (!$_POST['mode_pembahasan']) {
    $tanggal_pembahasan_or_null = 'NULL';
    echo div_alert('info', "Ujian tanpa pembahasan soal.");
  } elseif ($_POST['mode_pembahasan'] == 1) {
    // pembahasan saat semua ujian berakhir
    $tanggal_pembahasan_or_null = "'$global_akhir_ujian'";
    echo div_alert('info', "Pembahasan saat ujian ini berakhir untuk semua kelas [$global_akhir_ujian]");
  } else {
    // pembahasan pada waktu yang ditentukan
    $post_tanggal_pembahasan = "$_POST[tanggal_pembahasan] $_POST[jam_pembahasan]";
    if (strtotime($post_tanggal_pembahasan) < strtotime($global_akhir_ujian)) {
      // tidak boleh pembahasan < akhir ujian
      $post_tanggal_pembahasan = $global_akhir_ujian;
    }
    $tanggal_pembahasan_or_null =  "'$post_tanggal_pembahasan'";
    echo div_alert('info', "Pembahasan ujian ini pada waktu yang ditentukan [$tanggal_pembahasan_or_null]");
  }




  # =============================================
  # MAIN INSERT | UPDATE
  # =============================================
  $s = "INSERT INTO tb_paket (
    id,
    id_sesi,
    nama,
    id_pembuat,
    tanggal_pembahasan,
    sifat_ujian,
    kisi_kisi,
    max_attemp,
    durasi_ujian
  ) VALUES (
    $id_paket_or_new,
    $_POST[id_sesi],
    '$_POST[nama_paket]',
    $id_peserta,
    $tanggal_pembahasan_or_null,
    '$_POST[sifat_ujian]',
    $kisi_kisi_or_null,
    $_POST[max_attemp],
    $_POST[durasi_ujian]
  ) ON DUPLICATE KEY UPDATE 
    id_sesi = $_POST[id_sesi],
    nama = '$_POST[nama_paket]',
    id_pembuat = $id_peserta,
    tanggal_pembahasan = $tanggal_pembahasan_or_null,
    sifat_ujian = '$_POST[sifat_ujian]',
    kisi_kisi = $kisi_kisi_or_null,
    max_attemp = $_POST[max_attemp],
    durasi_ujian = $_POST[durasi_ujian]
  
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "$mode paket berhasil.");






  # =============================================
  # ARR_KELAS HANDLER
  # =============================================
  foreach ($_POST['arr_kelas'] as $kelas) {
    $is_check = $_POST['untuk_kelas'][$kelas] ?? '';
    $paket_kelas = $id_paket . '__' . $kelas;
    $awal_ujian = $kelas == 'INSTRUKTUR' ? 'CURRENT_TIMESTAMP' : $_POST['tanggal_ujian'][$kelas] . ' ' . $_POST['jam_ujian'][$kelas];
    if ($is_check || $kelas == 'INSTRUKTUR') {
      // insert anggota kelas 
      $s = "INSERT INTO tb_paket_kelas (
        paket_kelas,
        id_paket,
        kelas,
        awal_ujian
      ) VALUES (
        '$paket_kelas',
        $id_paket,
        '$kelas',
        '$awal_ujian'
      ) ON DUPLICATE KEY UPDATE
        id_paket = $id_paket,
        kelas = '$kelas',
        awal_ujian = '$awal_ujian'
      ";
      echo div_alert('success', "INSERT kelas $kelas sukses.");
    } else {
      // drop kelas
      $s = "DELETE FROM tb_paket_kelas WHERE paket_kelas = '$paket_kelas'";
      echo div_alert('success', "DROP kelas $kelas sukses.");
    }
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  } // foreach kelas


  jsurl('?manage_paket_soal', 2000);
  exit;
}




























# ================================================ -->
# MAIN SELECT PAKET PROPERTIES IF EDITING
# ================================================ -->
$arr_kelas = [];
$d_paket = [];

if ($id_paket) {
  $s = "SELECT 
  a.*,
  (
    SELECT COUNT(1) FROM tb_jawabans p 
    JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas  
    WHERE q.id_paket=a.id) count_submit
  FROM tb_paket a WHERE a.id=$id_paket";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    die("Data paket dengan id: $id_paket tidak ditemukan.");
  } else {
    $d_paket = mysqli_fetch_assoc($q);

    if ($d_paket['count_submit']) {
      echo div_alert('danger', "Paket soal ini tidak bisa lagi diedit karena sudah ada $d_paket[count_submit] $Peserta yang submit jawaban");
      echo "
        <script>
          $(function(){
            $('input').prop('disabled',1);
            $('select').prop('disabled',1);
            $('button').prop('disabled',1);
            $('textarea').prop('disabled',1);
          })
        </script>
      ";
    }
  }

  $s = "SELECT kelas, awal_ujian FROM tb_paket_kelas WHERE id_paket=$id_paket";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    // array anggota kelas
    array_push($arr_kelas, $d['kelas']);

    // current global akhir ujian
    $akhir_ujian = akhir_ujian($d['awal_ujian'], $d_paket['durasi_ujian']);
    if (strtotime($akhir_ujian) > strtotime($global_akhir_ujian)) $global_akhir_ujian = $akhir_ujian;
  }
}


























# ================================================ -->
# SELECT KODE SESI
# ================================================ -->
// aborted

# ================================================ -->
# SELECT KELAS DAN MISAL NAMA PAKET
# ================================================ -->
$s = "SELECT 
a.kelas,
b.semester,
b.prodi,
(
  SELECT awal_ujian 
  FROM tb_paket_kelas 
  WHERE id_paket='$id_paket' 
  AND kelas=a.kelas) awal_ujian 

FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas
WHERE id_room=$id_room ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = "<option value=all>Untuk Semua Kelas pada $Room ini</option>";
$info_prodi = '';
$info_semester = '';
$tr_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $info_prodi = $d['prodi'];
  $opt .= "<option value='$d[kelas]' >Untuk Kelas $d[kelas]</option>";

  $disabled = $d['kelas'] == 'INSTRUKTUR' ? 'disabled' : '';
  $checked = $d['kelas'] == 'INSTRUKTUR' ? 'checked' : '';
  $checked = in_array($d['kelas'], $arr_kelas) ?  'checked' : $checked;
  $checked = $mode == 'add' ?  'checked' : $checked;


  $awal_ujian = $d['awal_ujian'];

  if ($awal_ujian) {
    $tanggal_ujian = date('Y-m-d', strtotime($awal_ujian));
    $jam_ujian = date('H:i', strtotime($awal_ujian));
  } else {
    $tanggal_ujian = $today;
    $jam_ujian = '7:30';
  }

  $kelas = $d['kelas'];
  $eta = $awal_ujian ? eta2($awal_ujian) : '';

  $input_mulai_ujian = $d['kelas'] == 'INSTRUKTUR' ? "
    <div class='f12 miring abu'>$Trainer dapat mengakses paket ini seterusnya</div>
  " : "
    <div class='flexy'>
      <div>
        <input required type=date value='$tanggal_ujian' min='$today' class='form-control jadwal-berubah' name=tanggal_ujian[$kelas] id=tanggal_ujian__$d[kelas]>
      </div>
      <div>
        <input required type=time value='$jam_ujian' min='7:00' class='form-control jadwal-berubah' name=jam_ujian[$kelas] id=jam_ujian__$d[kelas]>
      </div>
      <div class='darkblue pt1'>
        $eta
      </div>
    </div>  
  ";


  $tr_kelas .= "
    <tr>
      <td>
        <input type=hidden name='arr_kelas[$kelas]' id='arr_kelas__$d[kelas]' value='$d[kelas]'>
        <label>
          <input type=checkbox name=untuk_kelas[$kelas] class='untuk_kelas' id=untuk_kelas__$d[kelas] $checked $disabled value=1>
          $d[kelas]
        </label>
      </td>
      <td>
        $input_mulai_ujian
      </td>
    </tr>
  ";
}
$gg = $ta_aktif % 2 == 0 ? 'Genap' : 'Ganjil';
$ta_gg = substr($ta_aktif, 0, 4) . ' ' . $gg;
$misal_nama_paket = "UTS $singkatan_room Semester 1 TA. $ta_gg";




$v = [
  'blok' => 'input-range',
  'label' => 'Durasi Ujian',
  'type' => 'number',
  'placeholder' => '...',
  'value' => $d_paket['durasi_ujian'] ?? 60,
  'required' => 1,
  'class' => 'mb2 f18 darkblue tengah',
  'min' => 10,
  'max' => 240,
  'minlength' => 0,
  'maxlength' => 0,
  'range' => [30, 40, 50, 60, 70, 80, 90],
  'satuan' => 'menit'

];

$div_range = '';
$min_range = 0;
$max_range = 0;
$i = 0;
foreach ($v['range'] as $key2 => $range_value) {
  $i++;
  if ($i == 1) $min_range = $range_value;
  $div_range .= "<div>$range_value</div>";
  $max_range = $range_value;
}
$value = $v['value'];
$val_range = $value ? $value : intval(($max_range - $min_range) / 2) + $min_range;
$step = $v['step'] ?? 1;
$placeholder = $v['placeholder'] ?? '...';
$type = $v['type'] ?? 'text';
$min = $v['min'] ?? '';
$max = $v['max'] ?? '';
$minlength = $v['minlength'] ?? '';
$maxlength = $v['maxlength'] ?? '';
$class = $v['class'] ?? '';
$satuan = $v['satuan'] ?? '';
$required = 'required'; // zzz default

$range_durasi = "
  <div class='flexy flex-center'>
    <div class='f14 darkblue miring pt1'>$v[label]</div>
    <div>
      <input 
        id='durasi_ujian' 
        name='durasi_ujian' 
        value='$value' 
        step='$step' 
        placeholder='$placeholder' 
        type='$type' 
        $required
        class='form-control mb2 $class' 
        min='$min' 
        max='$max' 
        minlength='$minlength' 
        maxlength='$maxlength' 
        style='max-width:100px'
      >          
    </div>
    <div class='f14 abu miring pt1'>$satuan</div>
  </div>
  <input type='range' class='form-range range' min='$min_range' max='$max_range' id='range__durasi_ujian' value='$val_range' step='$step' name=durasi_ujian>
  <div class='flexy flex-between f12 consolas abu'>
    $div_range
  </div>
";

$mode_pembahasan = 0; // default tidak ada pembahasan
$tanggal_pembahasan = $d_paket['tanggal_pembahasan'] ?? '';
if ($tanggal_pembahasan) {
  echo "$tanggal_pembahasan == $global_akhir_ujian ZZZ";
  if ($tanggal_pembahasan == $global_akhir_ujian) {
    $mode_pembahasan = 1; // saat semua ujian berakhir
  } else {
    $mode_pembahasan = 2; // pada tanggal tertentu
  }
}

$hide_blok_tanggal_pembahasan = $mode_pembahasan == 2 ? '' : 'hideit';

$global_akhir_ujian_show = date('M d, Y, H:i', strtotime($global_akhir_ujian));
$eta_global_akhir_ujian = eta2($global_akhir_ujian);


$tanggal_pembahasan = $d_paket['tanggal_pembahasan'] ?? '';
if (strtotime($tanggal_pembahasan) < strtotime($global_akhir_ujian)) {
  echo div_alert('danger', "
    Tanggal pembahasan invalid: $tanggal_pembahasan<hr>
    Auto-update with global_akhir_ujian: $global_akhir_ujian, id_paket: $id_paket
  ");

  $s = "UPDATE tb_paket SET tanggal_pembahasan='$global_akhir_ujian' WHERE id=$id_paket";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // jsurl('', 5000);
  exit;
}



# ================================================ -->
# OPTION MODE PEMBAHASAN
# ================================================ -->
$opt_mode_pembahasan = '';
$arr = [
  0 => 'Jangan tampilkan Kunci Jawaban setelah ujian',
  1 => 'Tampilkan Kunci Jawaban saat ujian berakhir',
  2 => 'Tampilkan Kunci Jawaban pada waktu tertentu',
];
foreach ($arr as $key => $value) {
  $selected = $key == $mode_pembahasan ? 'selected' : '';
  $opt_mode_pembahasan .= "<option value=$key $selected>$value</option>";
}

# ================================================ -->
# OPTION SIFAT UJIAN
# ================================================ -->
$opt_sifat_ujian = '';
$sifat_ujian = $d_paket['sifat_ujian'] ?? '';
$arr = [
  0 => 'Sifat Ujian Close Book',
  1 => 'Sifat Ujian Close Book, Open Kalkulator',
  2 => 'Sifat Ujian Open Book',
  3 => 'Sifat Ujian Open Book, Open Kalkulator',
  4 => 'Sifat Ujian Open Book, Open Internet',
];
foreach ($arr as $key => $value) {
  $selected = $key == $sifat_ujian ? 'selected' : '';
  $opt_sifat_ujian .= "<option value=$key $selected>$value</option>";
}


# ================================================ -->
# OPTION MAX ATTEMP
# ================================================ -->
$opt_max_attemp = '';
$max_attemp = $d_paket['max_attemp'] ?? 2; // default attemp 2x
$arr = [
  1 => 'Peserta hanya bisa ujian 1 kali (tidak bisa mengulang)',
  2 => 'Peserta bisa mencoba ujian hingga 2 kali mencoba',
  3 => 'Peserta bisa mencoba ujian hingga 3 kali mencoba',
  4 => 'Peserta bisa mencoba ujian hingga 4 kali mencoba',
  5 => 'Peserta bisa mencoba ujian hingga 5 kali mencoba',
  10 => 'Peserta bisa mencoba ujian hingga 10 kali mencoba',
];
foreach ($arr as $key => $value) {
  $selected = $key == $max_attemp ? 'selected' : '';
  $opt_max_attemp .= "<option value=$key $selected>$value</option>";
}

if ($mode == 'add') {
  $info_berakhir = '';
  $tanggal_pembahasan = date('Y-m-d H:i', strtotime('now') + 60 * 60);
} else {
  $info_berakhir = "
    <hr>
    <div class=tengah>
      Ujian akan berakhir pada: <span class=global_akhir_ujian_show>$global_akhir_ujian_show | $eta_global_akhir_ujian</span>
    </div>
  ";
}


# ================================================ -->
# FINAL ECHO || BLOK TAMBAH PAKET SOAL
# ================================================ -->
$nama_paket = $d_paket['nama'] ?? '';
$kisi_kisi = $d_paket['kisi_kisi'] ?? '';
$mode_pembahasan = $d_paket['mode_pembahasan'] ?? '';


if (isset($d_paket['tanggal_pembahasan'])) {
  $tgl_pembahasan = date('Y-m-d', strtotime($d_paket['tanggal_pembahasan']));
  $jam_pembahasan = date('H:i', strtotime($d_paket['tanggal_pembahasan']));
  $eta_pembahasan = str_replace('lagi', '', eta(strtotime($d_paket['tanggal_pembahasan']) - strtotime($global_akhir_ujian)));
  $eta_pembahasan = "
    <div class='tengah mb4'>
      $eta_pembahasan  sejak ujian berakhir kunci jawaban akan ditampilkan
    </div>
  ";
} else {
  $tgl_pembahasan =   date('Y-m-d', strtotime($global_akhir_ujian));
  $jam_pembahasan = date('H:i', strtotime($global_akhir_ujian));
  $eta_pembahasan = '';
}

echo "
  <!-- ================================================ -->
  <!-- FORM TAMBAH PAKET SOAL -->
  <!-- ================================================ -->
  <form method=post class='m0 hideita'>
    <div class='wadah gradasi-hijau'>
      <div class='abu f14 miring  mb4'>Form <span class=proper>$mode</span> Paket Soal</div>

      <input name=id_paket class='hideit' value=$id_paket>
      $select_id_sesi

      <input required minlength=10 maxlength=50 class='form-control mb2' placeholder='Enter Nama Paket Soal...' name=nama_paket id=nama_paket value='$nama_paket'>
      <div class='f12 abu mb4'>Misal: <span class='darkblue miring pointer' id=misal_nama_paket>$misal_nama_paket</span></div>


      <div class=wadah>
        <div class='abu miring mb1'>Paket ini boleh diakses oleh:</div>
        <table class=table>
          <thead>
            <th>Grup Kelas</th>
            <th>Awal Ujian (Tanggal / Pukul)</th>
          </thead>
          $tr_kelas
        </table>
      </div>


      <div class=wadah>
        $range_durasi
        $info_berakhir
      </div>

      <textarea 
        class='form-control mb2' 
        placeholder='Enter kisi-kisi ujian (opsional)... akan bisa dilihat oleh $Peserta sebelum ujian berlangsung.' 
        rows=4 
        name=kisi_kisi
      >$kisi_kisi</textarea>

      <div class=wadah>
        <div class='mb2 miring abu tengah'>Opsi-opsi Ujian</div>

        <select class='form-control mb2 tengah' name=sifat_ujian>
          $opt_sifat_ujian
        </select>

        <select class='form-control mb2 center' id=mode_pembahasan name=mode_pembahasan>
          $opt_mode_pembahasan
        </select>

        <div class='$hide_blok_tanggal_pembahasan mb4' id=blok_tanggal_pembahasan>
          <div class='flexy flex-center'>
            <div class=pt1>
              Tanggal
            </div>
            <div>
              <input required type=date value='$tgl_pembahasan' min='$tgl_pembahasan' class='form-control mb2' name=tanggal_pembahasan id=tanggal_pembahasan>
            </div>
            <div>
              <input required type=time value='$jam_pembahasan' class='form-control mb2' name=jam_pembahasan id=jam_pembahasan>
            </div>
            </div>
            $eta_pembahasan
        </div>

        <select class='form-control mb2 tengah' name=max_attemp>
          $opt_max_attemp
        </select>
        <select class='form-control mb2 tengah' name=wajib_polling>
          <option value=0>Tidak wajib polling untuk melihat Hasil Ujian</option>
          <option class=hideit value=uts>Wajib Poling UTS</option>
          <option class=hideit value=uas>Wajib Poling UAS</option>
        </select>
      </div>

      <button class='btn btn-primary w-100 proper' name=btn_simpan_paket_soal id=btn_simpan_paket_soal>$mode Paket Soal</button>

    </div>
  </form>
</td></tr>";






































?>
<script type="text/javascript">
  $(function() {
    $('#jangan_tampilkan_kj').click(function() {
      let val = $(this).prop('checked');
      console.log(val);
      $('#tanggal_pembahasan').prop('disabled', val);
      $('#jam_pembahasan').prop('disabled', val);
    });

    $('#misal_nama_paket').click(function() {
      $('#nama_paket').val($(this).text());
    });

    // checkbox untuk_kelas
    $('.untuk_kelas').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let kelas = rid[1];
      let is_checked = $(this).prop('checked');
      console.log(aksi, kelas, is_checked);

      $('#tanggal_ujian__' + kelas).prop('required', true);
      $('#tanggal_ujian__' + kelas).show();
      $('#jam_ujian__' + kelas).prop('required', true);
      $('#jam_ujian__' + kelas).show();

      if (!is_checked) {
        let y = confirm('Yakin tidak ingin menyertakan kelas ini ke Paket Soal?');
        if (!y) {
          $(this).prop('checked', true);
          return;
        } else {
          $('#tanggal_ujian__' + kelas).prop('required', false);
          $('#tanggal_ujian__' + kelas).fadeOut();
          $('#jam_ujian__' + kelas).prop('required', false);
          $('#jam_ujian__' + kelas).fadeOut();

        }
      }
    });

    // select mode_pembahasan
    $('#mode_pembahasan').change(function() {
      let val = $(this).val();
      // console.log(val, 'zzz');
      if (val == 2) {
        $('#blok_tanggal_pembahasan').show();
      } else {
        $('#blok_tanggal_pembahasan').hide();
      }
    });

    $('#range__durasi_ujian').change(function() {
      $('#durasi_ujian').val($(this).val());
    });

    $('.jadwal-berubah').change(function() {
      $('.global_akhir_ujian_show').html('<i class=darkblue>Jadwal berubah.</i>');
    });

  })
</script>