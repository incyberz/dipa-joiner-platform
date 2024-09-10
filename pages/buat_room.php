<?php
if ($id_role != 2) {
  echo div_alert('danger', 'Hanya instruktur yang berhak membuat Room baru');
  jsurl('?', 3000);
}
instruktur_only();
set_h2('Create Room', "
  <div class='tengah mb2'>
    <a href='?pilih_room'>$img_prev</a>
  </div>
  Welcome <u>$nama_peserta</u>! Silahkan isi form berikut untuk Pembuatan Room Baru!
");



// variabel awal
$nama_room = '';
$singkatan_room = '';
// $ta = date('Y');
$prodi = '';
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
        tahun_ajar,
        nama,
        singkatan,
        created_by
      ) VALUES (
        $new_id,
        $ta,
        '$_POST[nama_room]',
        '$_POST[singkatan_room]',
        $id_peserta
      )";
      echo '<pre>';
      var_dump($s);
      echo '</pre>';
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo div_alert('success', 'Room berhasil dibuat.');

      // assign room kelas
      $s = "INSERT INTO tb_room_kelas (
        ta,
        id_room,
        kelas
      ) VALUES (
        $ta,
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
  <input class='form-control mt1' required minlength='10' maxlength='30' name=nama_room value='$nama_room' placeholder='Nama Room...'>
  <div class='mt1 mb2 f12 abu'>Contoh: Pemrograman Web II (Laravel) 2024. 10 s.d 30 karakter</div>

  <div>Singkatan Room</div>
  <input class='form-control mt1' required minlength='3' maxlength='10' name=singkatan_room value='$singkatan_room' placeholder='Singkatan...'>
  <div class='mt1 mb2 f12 abu'>Contoh: PWeb2-24, tanpa spasi, 3 s.d 10 karakter</div>



  <button class='btn btn-primary w-100' name=btn_buat_room>Buat Room </button>
</form>
";

/// ZZZ HERE zzz here
// make upercase singkatan room