<?php
instruktur_only();
set_h2('Create Room', "
  <div class='tengah mb2'>
    <a href='?pilih_room'>$img_prev</a>
  </div>
  Welcome <u>$nama_peserta</u>! Silahkan isi form berikut untuk Pembuatan Room Baru!
");
include 'include/date_managements.php';


// variabel awal
$nama_room = 'ZZZ Pemrograman Web II (Laravel) 2024';
$singkatan_room = 'ZZZ-24';
$tahun_ajar = date('Y');
$prodi = 'LEMBAGAZZZ';
$jumlah_sesi = 16;
$pukul = '08:00:00';




if (isset($_POST['btn_buat_room'])) {
  foreach ($_POST as $key => $value) {
    $_POST[$key] = clean_sql($value);
  }
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  echolog('checking duplicate');
  $s = "SELECT 1 FROM tb_room WHERE nama LIKE '%$_POST[nama_room]%'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Nama Room: <u>$_POST[nama_room]</u> sudah ada pada database<hr>Silahkan gunakan yang lain.");
  } else {
    $s = "SELECT 1 FROM tb_room WHERE singkatan LIKE '%$_POST[singkatan_room]%'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (mysqli_num_rows($q)) {
      echo div_alert('danger', "Singkatan Room: <u>$_POST[singkatan_room]</u> sudah ada pada database<hr>Silahkan gunakan yang lain.");
    } else {

      echolog('generate new id_room');
      $s = "SELECT MAX(id) as max_id FROM tb_room";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $d = mysqli_fetch_assoc($q);
      $new_id = $d['max_id'] + 1;



      echolog('Creating room');
      $s = "INSERT INTO tb_room (
        id,
        nama,
        singkatan,
        lembaga,
        tahun_ajar,
        created_by
      ) VALUES (
        $new_id,
        '$_POST[nama_room]',
        '$_POST[singkatan_room]',
        '$_POST[lembaga]',
        $_POST[tahun_ajar]$_POST[gg],
        $id_peserta
      )";
      // echo '<pre>';
      // var_dump($s);
      // echo '</pre>';
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', 'Room berhasil dibuat.');

      // assign room kelas
      $s = "INSERT INTO tb_room_kelas (
        id_room,
        kelas
      ) VALUES (
        $new_id,
        '$kelas'
      )
      ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', "Assign kelas: $kelas ke room baru berhasil.");


      $_SESSION['dipa_id_room'] = $new_id;
      echo div_alert('success', "Assign kelas: $kelas ke room baru berhasil.");
      jsurl('?', 3000);
      exit;
    }
  }
}



$add = (16 + 2) * 7;
$ahad_p17 = date('Y-m-d', strtotime("+$add day", strtotime($ahad_skg)));



echo "
<form method='post' class='wadah gradasi-hijau'>
  <div class='sub_form'>Form Buat Room Baru</div>
  <div>Nama Room</div>
  <input class='form-control mt1' required minlength='10' maxlength='30' name=nama_room value='$nama_room'>
  <div class='mt1 mb2 f12 abu'>Contoh: Pemrograman Web II (Laravel) 2024. 10 s.d 30 karakter</div>

  <div>Singkatan Room</div>
  <input class='form-control mt1' required minlength='3' maxlength='10' name=singkatan_room value='$singkatan_room' >
  <div class='mt1 mb2 f12 abu'>Contoh: PWeb2-24, tanpa spasi, 3 s.d 10 karakter</div>

  <div class='mb1'>Tahun Ajar</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=number name=tahun_ajar min=2024 max=2025 value=$tahun_ajar>
    </div>
    <div>
      <select name='gg' class='form-control'>
        <option value='1'>Ganjil</option>
        <option value='2'>Genap</option>
      </select>
    </div>
    <div>
      <input required class='form-control' required name=lembaga minlength='3' maxlength='30' placeholder='Prodi/Jurusan/Lembaga...' value='$prodi'>
    </div>

  </div>
  <div class='mt1 mb2 f12 abu'>Contoh: Tahun ajar 2023 Genap, Prodi MBS, Fakultas FEBI</div>


  <button class='btn btn-primary w-100' name=btn_buat_room>Buat Room </button>
</form>
";
