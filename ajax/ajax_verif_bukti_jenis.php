<?php 
include 'instruktur_only.php';
include '../conn.php';
// include 'get_id_peserta.php';

# ================================================
# GET VERIF VARIABLES
# ================================================
$aksi = $_GET['aksi'] ?? die(erid("aksi"));if($aksi=='')die(erid('aksi(NULL)'));
$id = $_GET['id'] ?? die(erid("id"));if($id=='')die(erid('id(NULL)'));
$jenis = $_GET['jenis'] ?? die(erid("jenis"));if($jenis=='')die(erid('jenis(NULL)'));
$alasan_reject = $_GET['alasan_reject'] ?? die(erid("alasan_reject"));


# ================================================
# CRUD HANDLER
# ================================================
if($aksi=='accept'){
  $status = 1;
} elseif($aksi=='reject'){
  $status = -1;
}else{
  die("Unhandle aksi: $aksi pada ajax.");
}

$alasan_reject = $alasan_reject=='' ? 'NULL' : "'$alasan_reject'";
$s =  "UPDATE tb_bukti_$jenis SET 
tanggal_verifikasi = CURRENT_TIMESTAMP,
verified_by = $id_peserta, 
status = $status,  
alasan_reject = $alasan_reject  
WHERE id=$id ";


// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>