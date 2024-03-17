<?php
if (isset($_POST['btn_update_jenis'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $id = $_POST['btn_update_jenis'];
  unset($_POST['btn_update_jenis']);

  $pairs = '';
  foreach ($_POST as $key => $value) {
    $value = str_replace('\'', '`', $value);
    $value_quote = $value ? "'$value'" : 'NULL';
    $pairs .= "$key=$value_quote,";
  }
  $pairs .= '__';
  $pairs = str_replace(',__', '', $pairs);

  $s = "UPDATE tb_$jenis SET $pairs WHERE id=$id";
  // die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

if (isset($_POST['btn_update_assign'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  $untuk_kelas = $_POST['untuk_kelas'] ?? die(erid('untuk_kelas'));
  $s = "SELECT id as id_room_kelas FROM tb_room_kelas WHERE kelas='$untuk_kelas' AND id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) != 1) die('id_room_kelas harus unik pada btn_update_assign processor');
  $d = mysqli_fetch_assoc($q);
  $target_id_room_kelas = $d['id_room_kelas'];
  echo div_alert('success', "Select target_id_room_kelas : $target_id_room_kelas");



  $id_assign = $_POST['btn_update_assign'];
  unset($_POST['btn_update_assign']);

  $s = "SELECT id_$jenis FROM tb_assign_$jenis WHERE id=$id_assign";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) != 1) die('id_assign harus unik pada btn_update_assign processor');
  $d = mysqli_fetch_assoc($q);
  $id_jenis = $d['id_' . $jenis];
  echo div_alert('success', "Select id_$jenis : $id_jenis");

  // update is_wajib for all kelas
  $is_wajib = $_POST['is_wajib'] ?? die(erid('is_wajib'));
  $is_wajib_or_null = $is_wajib ? $is_wajib : 'NULL';
  $s = "UPDATE tb_assign_$jenis SET is_wajib = $is_wajib_or_null WHERE id_$jenis=$id_jenis ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Update is_wajib untuk semua room kelas berhasil.");



  // update tanggal for target kelas only
  $pairs = '';
  foreach ($_POST as $key => $value) {
    $value = str_replace('\'', '`', $value);
    $value_quote = $value ? "'$value'" : 'NULL';
    $pairs .= "$key=$value_quote,";
  }
  $pairs .= '__';
  $pairs = str_replace(',__', '', $pairs);

  $s = "UPDATE tb_assign_$jenis SET tanggal='$_POST[tanggal]' WHERE id_$jenis=$id_jenis AND id_room_kelas=$target_id_room_kelas";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', "Update tanggal assign untuk kelas $untuk_kelas berhasil.");
  // jsurl();
}
