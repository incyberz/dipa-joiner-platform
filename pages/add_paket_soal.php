<?php
instruktur_only();
$null = '<span class="abu f12 miring consolas">null</span>';

$judul = 'Add Paket Soal';
set_h2($judul, "Paket Soal adalah wadah untuk soal-soal yang akan diujikan ke tiap Grup Kelas<div class=mt2><a href='?manage_paket_soal' >$img_prev</a></div>");





# ================================================ -->
# PROCESSOR
# ================================================ -->
if (isset($_POST['btn_simpan_paket_soal'])) {
  // clean SQL
  foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);
  # =============================================
  # SAVE TO DB
  # =============================================
  $id_paket_for_update = $_POST['id_paket_soal_for_update'];
  $nama = $_POST['nama_paket'];
  $max_attemp = $_POST['max_attemp'];
  $untuk_kelas = $_POST['untuk_kelas'];
  $awal_ujian = $_POST['awal_ujian'];
  $akhir_ujian = $_POST['akhir_ujian'];
  $tanggal_ujian = $_POST['tanggal_ujian'];
  $tanggal_pembahasan = $_POST['tanggal_pembahasan'];
  $awal_pembahasan = $_POST['awal_pembahasan'];
  $kode_sesi = $_POST['kode_sesi'];
  $sifat_ujian = $_POST['sifat_ujian'];
  $kisi_kisi = $_POST['kisi_kisi'];
  $id_pembuat = $id_peserta;
  $kisi_kisi_or_null = $kisi_kisi ? "'$kisi_kisi'" : 'NULL';

  // SQL Update or Insert
  if ($id_paket_for_update) {
    $s = "UPDATE tb_paket SET 
      soal='$soal',
      opsies='$opsies',
      kjs='$kjs',
      pembahasan=$pembahasan_or_null 
    WHERE id=$id_paket_for_update
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Update paket soal berhasil.');
    jsurl('', 1000);
    exit;
  } else {
    if ($untuk_kelas == 'all') {
      $s = "SELECT a.kelas FROM tb_room_kelas a 
      JOIN tb_kelas b ON a.kelas=b.kelas 
      WHERE a.id_room=$id_room AND b.status=1 ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $arr_untuk_kelas = [];
      while ($d = mysqli_fetch_assoc($q)) {
        array_push($arr_untuk_kelas, $d['kelas']);
      }
    } else {
      $arr_untuk_kelas = [$untuk_kelas];
    }

    foreach ($arr_untuk_kelas as $key => $untuk_kelas) {
      $s = "INSERT INTO tb_paket (
        id_room,
        nama,
        kelas,
        id_pembuat,
        awal_ujian,
        akhir_ujian,
        tanggal_pembahasan,
        kode_sesi,
        sifat_ujian,
        kisi_kisi,
        max_attemp
      ) VALUES (
        $id_room,
        '$nama',
        '$untuk_kelas',
        $id_pembuat,
        '$tanggal_ujian $awal_ujian',
        '$tanggal_ujian $akhir_ujian',
        '$tanggal_pembahasan $awal_pembahasan',
        '$kode_sesi',
        '$sifat_ujian',
        $kisi_kisi_or_null,
        $max_attemp
      )";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', "Simpan paket untuk kelas $untuk_kelas berhasil.");
    }
    jsurl('', 2000);
    exit;
  }
}









# ================================================ -->
# SELECT KODE SESI
# ================================================ -->
$s = "SELECT * FROM tb_kode_sesi ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = '';
while ($d = mysqli_fetch_assoc($q)) {
  $selected = $d['kode_sesi'] == 'uts' ? 'selected' : '';
  $opt .= "<option value='$d[kode_sesi]' $selected>Untuk event $d[nama]</option>";
}
$select_kode_sesi = "<select name=kode_sesi id=kode_sesi class='form-control mb2'>$opt</select>";

# ================================================ -->
# SELECT KELAS DAN MISAL NAMA PAKET
# ================================================ -->
$s = "SELECT a.kelas,b.semester,b.prodi FROM tb_room_kelas a 
JOIN tb_kelas b ON a.kelas=b.kelas
WHERE id_room=$id_room ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt = '<option value=all>Untuk Semua Kelas pada Room ini</option>';
$info_prodi = '';
$info_semester = '';
$cek_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $info_prodi = $d['prodi'];
  $info_semester = $d['semester'];
  $opt .= "<option value='$d[kelas]' >Untuk Kelas $d[kelas]</option>";

  $disabled = $d['kelas'] == 'INSTRUKTUR' ? 'disabled' : '';
  $cek_kelas .= "
    <div>
      <label>
        <input type=checkbox name=untuk_kelas[] class='untuk_kelas' id=untuk_kelas__$d[kelas] checked $disabled>
        $d[kelas]
      </label>
    </div>
  ";
}
$gg = $tahun_ajar % 2 == 0 ? 'Genap' : 'Ganjil';
$ta_gg = substr($tahun_ajar, 0, 4) . ' ' . $gg;
$misal_nama_paket = "UTS $room $info_prodi Semester $info_semester TA. $ta_gg";




$v = [
  'blok' => 'input-range',
  'label' => 'Durasi Ujian',
  'type' => 'number',
  'placeholder' => '...',
  'value' => $paket['durasi_ujian'] ?? 60,
  'required' => 1,
  'class' => 'mb2 f18 darkblue tengah',
  'min' => 40,
  'max' => 120,
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
$value = $v['value'] ?? $paket[$key];
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
        id='$key' 
        name='$key' 
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
  <input type='range' class='form-range range' min='$min_range' max='$max_range' id='range__$key' value='$val_range' step='$step'>
  <div class='flexy flex-between f12 consolas abu'>
    $div_range
  </div>
";


# ================================================ -->
# BLOK TAMBAH PAKET SOAL
# ================================================ -->
echo "
  <!-- ================================================ -->
  <!-- FORM TAMBAH PAKET SOAL -->
  <!-- ================================================ -->
  <form method=post class='m0 hideita'>
    <div class='wadah gradasi-hijau'>
      <div class='abu f14 miring  mb4'>Form <span class=Tambah>Tambah</span> Paket Soal</div>

      <input name=id_paket_for_update placeholder='id_paket_for_update'>
      $select_kode_sesi

      <input required minlength=10 maxlength=50 class='form-control mb2' placeholder='Enter Nama Paket Soal...' name=nama_paket id=nama_paket>
      <div class='f12 abu mb4'>Misal: <span class='darkblue miring pointer' id=misal_nama_paket>$misal_nama_paket</span></div>


      <div class=wadah>
        <div class='abu miring mb1'>Paket ini boleh diakses oleh Kelas:</div>
        $cek_kelas
      </div>

      <div class=wadah>
        <div class='flexy flex-center' style='flex-wrap:wrap'>
          <div class='darkblue miring pt1'>Mulai Ujian tanggal</div>
          <div>
            <input required type=date value='$today' class='form-control' name=tanggal_ujian>
          </div>
          <div>
            <input required type=time value='07:30' class='form-control' name=awal_ujian>
          </div>
          <div class='darkblue pt1'>
            3 hari lagi
          </div>
        </div>
      </div>

      <div class=wadah>$range_durasi</div>

      <textarea class='form-control mb2' placeholder='Enter kisi-kisi ujian (opsional)... akan bisa dilihat oleh peserta sebelum ujian berlangsung.' rows=4 name=kisi_kisi></textarea>

      <div class=wadah>

        <select class='form-control mb2 center' id=select_waktu_pembahasan>
          <option value=0>Jangan tampilkan Kunci Jawaban setelah Ujian</option>
          <option value=1 selected>Tampilkan Kunci Jawaban setelah Semua Kelas telah ujian</option>
          <option value=2>Tampilkan Kunci Jawaban pada waktu tertentu</option>
        </select>


        <div class='flexy flex-center'>
          <div class=pt1>
            Pada tanggal
          </div>
          <div>
            <input required type=date value='$today' class='form-control mb2' name=tanggal_pembahasan id=tanggal_pembahasan>
          </div>
          <div>
            <input required type=time value='09:10' class='form-control mb2' name=awal_pembahasan id=awal_pembahasan>
          </div>
        </div>
      </div>
      <div class=wadah>
        <select class='form-control mb2' name=sifat_ujian>
          <option>Sifat Ujian Close Book</option>
          <option>Sifat Ujian Open Book</option>
          <option>Sifat Ujian Close Book, Open Kalkulator</option>
          <option>Sifat Ujian Open Book, Open Kalkulator</option>
          <option>Sifat Ujian Open Book, Open Internet</option>
        </select>

        <select class='form-control mb2' name=max_attemp>
          <option value=1>Peserta hanya bisa ujian 1 kali (tidak bisa mengulang)</option>
          <option value=2 selected>Peserta bisa mencoba ujian hingga 2 kali mencoba</option>
          <option value=3 >Peserta bisa mencoba ujian hingga 3 kali mencoba</option>
          <option value=4 >Peserta bisa mencoba ujian hingga 4 kali mencoba</option>
          <option value=5 >Peserta bisa mencoba ujian hingga 5 kali mencoba</option>
          <option value=10 >Peserta bisa mencoba ujian hingga 10 kali mencoba</option>
        </select>
        <select class='form-control mb2' name=wajib_polling>
          <option value=0>Tidak wajib polling untuk melihat Hasil Ujian</option>
          <option value=uts>Wajib Poling UTS</option>
          <option value=uas>Wajib Poling UAS</option>
        </select>
      </div>

      <button class='btn btn-primary w-100' name=btn_simpan_paket_soal id=btn_simpan_paket_soal>Simpan Paket Soal</button>

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
      $('#awal_pembahasan').prop('disabled', val);
    });

    $('#misal_nama_paket').click(function() {
      $('#nama_paket').val($(this).text());
    });


  })
</script>