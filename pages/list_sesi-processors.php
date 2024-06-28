<?php
$s = "SELECT 1 FROM tb_sesi WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_count_sesi = mysqli_num_rows($q);
if (isset($_POST['btn_tambah_sesi'])) {
  $new_count_sesi = $total_count_sesi + 1;
  $s = "INSERT INTO tb_sesi (id_room,no,nama) VALUES ($id_room,$new_count_sesi,'NEW SESI $new_count_sesi')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Sesi Baru berhasil dibuat.');
  jsurl();
  exit;
}
