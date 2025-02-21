<?php
if (isset($_POST['btn_add_kelas'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  $arr = $_POST['kelas'] ?? [];
  if ($arr) {
    $s = "SELECT 1 FROM tb_kelas WHERE kelas='$arr[kelas]'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echolog("checking duplicate kelas: $arr[kelas]");

    if (mysqli_num_rows($q)) {
      echo div_alert('danger', "Kelas $arr[kelas] sudah ada di database. Silahkan diubah!");
      jsurl('', 3000);
    } else {
      $koloms = '__';
      $isis = '__';
      foreach ($arr as $kolom => $isi) {
        $koloms .= ",$kolom";
        $isis .= ",'$isi'";
      }
      $koloms = str_replace('__,', '', $koloms);
      $isis = strtoupper(str_replace('__,', '', $isis));


      $s = "INSERT INTO tb_kelas ($koloms,ta) VALUES ($isis,$ta_aktif)";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echolog("Executing: $s");
    }


    echo div_alert('success', 'Berhasil menambahkan grup kelas.');
    jsurl('', 3000);
  } else {
    jsurl();
  }
}
