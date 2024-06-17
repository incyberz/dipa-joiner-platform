<?php
# ============================================================
# PROCESSORS
# ============================================================
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
    $value_quote = ($value == '-' || $value == $cara_pengumpulan_default) ? 'NULL' : $value_quote;
    $pairs .= "$key=$value_quote,";
  }
  $pairs .= '__';
  $pairs = str_replace(',__', '', $pairs);

  $s = "UPDATE tb_$jenis SET $pairs WHERE id=$id";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

if (isset($_POST['btn_update_assign'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  foreach ($_POST['tanggal_assign'] as $id_assign => $tanggal_assign) {
    $is_wajib = $_POST['is_wajib'] == 1 ? 1 : 'NULL';
    $s = "UPDATE tb_assign_latihan SET
      tanggal = '$tanggal_assign',
      is_wajib = $is_wajib 
      WHERE id = $id_assign
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }

  echo div_alert('success', "Update rule assign sukses.");
  jsurl('', 2000);
}
