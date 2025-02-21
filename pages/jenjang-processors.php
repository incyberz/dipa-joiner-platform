<?php
if (isset($_POST['btn_save_jenjang'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  foreach ($_POST['jenjang'] as $cid_room => $jenjang) {
    if ($jenjang) {
      $s = "UPDATE tb_room SET jenjang='$jenjang' WHERE id=$cid_room";
      echolog($s);
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
  }
  jsurl();
  exit;
}
if (isset($_POST['btn_save_jenis'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  foreach ($_POST['jenis'] as $cid_room => $jenis) {
    if ($jenis) {
      $s = "UPDATE tb_room SET jenis='$jenis' WHERE id=$cid_room";
      echolog($s);
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
  }
  jsurl();
  exit;
}

if (isset($_POST['btn_add_room_jenis'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  $jenis = $_POST['btn_add_room_jenis'];
  $nama = strip_tags(strtoupper(trim($_POST['nama'])));
  $singkatan = str_replace(' ', '-', strip_tags(strtoupper(trim($_POST['singkatan']))));
  $lembaga = strip_tags(strtoupper(trim($_POST['lembaga'])));

  $s = "INSERT INTO tb_room (
    nama,
    singkatan,
    ta,
    lembaga,
    created_by,
    jenjang,
    jenis
  ) VALUES (
    '$nama',
    '$singkatan',
    '$ta_aktif',
    '$lembaga',
    '$id_peserta',
    '$jenjang',
    '$jenis'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


  // foreach ($_POST['jenis'] as $cid_room => $jenis) {
  //   if ($jenis) {
  //     $s = "UPDATE tb_room SET jenis='$jenis' WHERE id=$cid_room";
  //     echolog($s);
  //   }
  // }
  jsurl();
  exit;
}
