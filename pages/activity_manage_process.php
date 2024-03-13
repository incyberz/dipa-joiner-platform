<?php
if (isset($_POST['btn_update_jenis'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

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

  $id = $_POST['btn_update_assign'];
  unset($_POST['btn_update_assign']);

  $pairs = '';
  foreach ($_POST as $key => $value) {
    $value = str_replace('\'', '`', $value);
    $value_quote = $value ? "'$value'" : 'NULL';
    $pairs .= "$key=$value_quote,";
  }
  $pairs .= '__';
  $pairs = str_replace(',__', '', $pairs);

  $s = "UPDATE tb_assign_$jenis SET $pairs WHERE id=$id";
  die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // jsurl();
}
