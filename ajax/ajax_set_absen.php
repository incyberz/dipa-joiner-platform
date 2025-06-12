<?php
include 'instruktur_only.php';

# ================================================
# GET VERIF VARIABLES
# ================================================
$id_peserta = $_GET['id_peserta'] ?? die(erid("id_peserta"));
if (!$id_peserta) die(erid('id_peserta(NULL)'));
$id_sesi = $_GET['id_sesi'] ?? die(erid("id_sesi"));
if (!$id_sesi) die(erid('id_sesi(NULL)'));
$kode_absen = $_GET['kode_absen'] ?? die(erid("kode_absen"));

# ================================================
# CRUD HANDLER
# ================================================
$id = "$id_sesi-$id_peserta";
if ($kode_absen > 0) {
  $s = "DELETE FROM tb_absen WHERE id='$id'";
} else {
  $s = "INSERT INTO tb_absen 
  (id,id_peserta,id_sesi,absen) VALUES 
  ('$id',$id_peserta,$id_sesi,$kode_absen)
  ON DUPLICATE KEY UPDATE 
  absen=$kode_absen, 
  tanggal=NOW()
  ";
}
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'OK';
