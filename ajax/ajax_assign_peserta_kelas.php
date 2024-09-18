<?php
include 'instruktur_only.php';

$aksi = $_GET['aksi'] ?? die(erid('aksi'));
if (!$aksi) die(erid('aksi::empty'));
$kelas = $_GET['kelas'] ?? die(erid('kelas'));
if (!$kelas) die(erid('kelas::empty'));
$id_peserta = $_GET['id_peserta'] ?? die(erid('id_peserta'));
if (!$id_peserta) die(erid('id_peserta::empty'));

if ($aksi == 'drop') {
  $s = "DELETE FROM tb_kelas_peserta WHERE kelas='$kelas' AND id_peserta=$id_peserta";
} elseif ($aksi == 'assign') {

  $s = "SELECT 1 FROM tb_kelas_peserta WHERE kelas = '$kelas' AND id_peserta = $id_peserta";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    die('sukses');
  } else {
    $s = "INSERT INTO tb_kelas_peserta (kelas,id_peserta) VALUES ('$kelas',$id_peserta)";
  }
}

// die($s);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'sukses';
