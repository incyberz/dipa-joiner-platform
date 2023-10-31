<?php
include 'instruktur_only.php';
include '../conn.php';

$id_peserta = $_GET['id_peserta'] ?? die(erid('id_peserta'));
$s = "DELETE FROM tb_peserta WHERE id='$id_peserta'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
echo 'sukses';