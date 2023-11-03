<?php 
include 'instruktur_only.php';
include '../conn.php';

# ================================================
# GET VERIF VARIABLES
# ================================================
$aksi = $_GET['aksi'] ?? die(erid("aksi"));if($aksi=='')die(erid('aksi(NULL)'));
$id_peserta = $_GET['id_peserta'] ?? die(erid("id_peserta"));if($id_peserta=='')die(erid('id_peserta(NULL)'));

$kode = $aksi=='accept' ? 1 : 'NULL';
$kode = $aksi=='formal' ? 2 : $kode;
$kode = $aksi=='reject' ? -1 : $kode;


# ================================================
# CRUD HANDLER
# ================================================
$s =  "UPDATE tb_peserta SET profil_ok=$kode WHERE id=$id_peserta";
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>