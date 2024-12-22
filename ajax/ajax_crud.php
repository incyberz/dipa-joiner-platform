<?php
include 'instruktur_only.php';
include '../includes/udef.php';

# ================================================
# GET CRUD VARIABLES
# ================================================
$tb = $_GET['tb'] ?? udef('tb');
$aksi = $_GET['aksi'] ?? udef('aksi');
$field_id_value = $_GET['field_id_value'] ?? udef('field_id_value');

$field_id = $_GET['field_id'] ?? 'id'; // default field id



# ================================================
# CRUD HANDLER
# ================================================
if ($aksi == 'hapus') {
  $s = "DELETE FROM tb_sesi WHERE id=$id";
} elseif ($aksi == 'ubah') {
  $field_target = $_GET['field_target'] ?? udef('field_target');
  $isi_baru = $_GET['isi_baru'] ?? udef('isi_baru');
  $isi_baru = $isi_baru == 'null' ? 'null' : "'$isi_baru'";

  $s = "UPDATE tb_$tb SET $field_target = $isi_baru WHERE $field_id=$field_id_value";
} else {
  die("aksi $aksi belum terdapat handler.");
}

$q = mysqli_query($cn, $s) or die(`Error @CRUD-AJAX:\n\n` . mysqli_error($cn));
die('sukses');
