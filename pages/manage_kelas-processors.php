<?php
# ====================================================
# PROCESSOR: ASSIGN ROOM KELAS
# ====================================================
if (isset($_POST['btn_assign_room_kelas'])) {
  $kelas = $_POST['btn_assign_room_kelas'];
  $s = "SELECT 1 FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$kelas'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    $s = "INSERT INTO tb_room_kelas (kelas,id_room,ta) VALUES ('$kelas',$id_room,$ta)";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Assign $Room Kelas sukses.");
  } else {
    echo div_alert('danger', "Kelas sudah terdaftar pada $Room ini.");
  }
  jsurl();
}

# ====================================================
# PROCESSOR: DROP ROOM KELAS
# ====================================================
if (isset($_POST['btn_drop_room_kelas'])) {

  // echo div_alert('danger', "DROPPING ROOM KELAS dapat berhasil jika dan hanya jika tidak ada $Peserta yang terdaftar pada kelas ini.<hr>Hubungi Master $Trainer (Developer) Jika ingin menghapus kelas aktif (yang sudah berjalan) dari $Room ini.");

  $s = "DELETE FROM tb_room_kelas WHERE id_room=$id_room AND kelas='$_POST[btn_drop_room_kelas]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Drop $Room Kelas sukses.");
  jsurl();
}

# ============================================================
# DELETE KELAS
# ============================================================
if (isset($_POST['btn_delete_kelas'])) {
  $s = "SELECT 1 FROM tb_room_kelas WHERE kelas='$_POST[btn_delete_kelas]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Kelas [ $_POST[btn_delete_kelas] ] sudah dipakai di $Room lain.");
  } else {
    $s = "DELETE FROM tb_kelas WHERE kelas='$_POST[btn_delete_kelas]'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Delete Kelas sukses.");
    jsurl();
  }
}

if (isset($_POST['btn_add_kelas'])) {

  $prodi = strtoupper(str_replace(' ', '', $_POST['prodi']));

  $NAMA = "$_POST[jenjang]-$prodi-$_POST[sub_kelas]-$_POST[shift]-SM$_POST[semester]-$ta_aktif";

  $s = "SELECT 1 FROM tb_kelas WHERE kelas='$NAMA'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Kelas [ $NAMA ] sudah ada di database. <a href='?manage_kelas&ta=$ta_aktif&mode=add'>Silahkan pakai data lain</a>!");
  } else {

    $fakultas = strtoupper($_POST['fakultas']);
    $nama_prodi = strtoupper($_POST['nama_prodi']);
    $caption = $_POST['caption'] ? strtoupper("'$_POST[caption]'") : $NAMA;

    $s = "INSERT INTO tb_kelas (
      kelas,
      prodi,
      sub_kelas,
      shift,
      semester,
      ta,
      fakultas,
      jenjang,
      nama_prodi,
      status,
      caption
    ) VALUES (
      '$NAMA',
      '$prodi',
      '$_POST[sub_kelas]',
      '$_POST[shift]',
      '$_POST[semester]',
      '$ta_aktif',
      '$fakultas',
      '$_POST[jenjang]',
      '$nama_prodi',
      '1',
      '$caption'
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Insert sukses.');
    jsurl("?manage_kelas&ta=$ta_aktif", 2000);
  }
}
