<?php
session_start();
include '../conn.php';

$nama_proyek = strip_tags(addslashes(strtoupper($_GET['nama_proyek'])));
$id_role = $_SESSION['dipa_id_role'];

$get_id_peserta = $_GET['id_peserta'] ?? die(erid('id_peserta'));
$get_nama_proyek = $_GET['nama_proyek'] ?? die(erid('nama_proyek'));

$s = "SELECT username FROM tb_peserta WHERE id=$get_id_peserta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Data Peserta tidak ditemukan'));
$d = mysqli_fetch_assoc($q);
$get_username = $d['username'];


$session_id_peserta = $_SESSION['dipa_id_peserta'];
$session_id_room = $_SESSION['dipa_id_room'];
$session_username = $_SESSION['dipa_username'];


if (strtolower($get_username) != strtolower($session_username)) {
  echo "get_username:$get_username != session_username:$session_username\n";
  echo 'gak boleh ngedit punya orang ya!';
} else {

  $s = "SELECT 1 FROM tb_proyek WHERE id_peserta=$get_id_peserta AND id_room=$session_id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $s = "UPDATE tb_proyek SET nama = '$nama_proyek' WHERE id_peserta=$get_id_peserta AND id_room=$session_id_room";
  } else {
    $s = "INSERT INTO tb_proyek (
      id_peserta,
      id_room,
      nama
    ) VALUES (
      $get_id_peserta,
      $session_id_room,
      '$nama_proyek'
    )
    ";
  }


  // die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo 'sukses';
}
