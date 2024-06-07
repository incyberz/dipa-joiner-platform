<?php
include 'instruktur_only.php';

$aksi = $_GET['aksi'] ?? die(erid('aksi'));
if (!$aksi) die(erid('aksi::empty'));
$id_soal = $_GET['id_soal'] ?? die(erid('id_soal'));
if (!$id_soal) die(erid('id_soal::empty'));
$id_paket = $_GET['id_paket'] ?? die(erid('id_paket'));
if (!$id_paket) die(erid('id_paket::empty'));

if ($aksi == 'drop') {
  // proses drop
  $s = "DELETE FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
} elseif ($aksi == 'assign') {
  // proses assign
  $s = "SELECT 1 FROM tb_assign_soal WHERE id_soal=$id_soal AND id_paket=$id_paket";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
  } else {
    $s = "INSERT INTO tb_assign_soal (id_soal,id_paket) VALUES ($id_soal,$id_paket)";
  }
}
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'sukses';
