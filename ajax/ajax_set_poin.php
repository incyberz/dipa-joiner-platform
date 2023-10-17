<?php 
include 'instruktur_only.php';
include '../conn.php';
// include 'get_id_peserta.php';

# ================================================
# GET VERIF VARIABLES
# ================================================
$tabel = $_GET['tabel'] ?? die(erid("tabel"));if($tabel=='')die(erid('tabel(NULL)'));
$nilai_baru = $_GET['nilai_baru'] ?? die(erid("nilai_baru"));if($nilai_baru=='')die(erid('nilai_baru(NULL)'));
$id = $_GET['id'] ?? die(erid("id"));if($id=='')die(erid('id(NULL)'));


# ================================================
# CRUD HANDLER
# ================================================
$s =  "UPDATE tb_$tabel SET 
verif_date = CURRENT_TIMESTAMP,
verif_by = $id_peserta, 
verif_status = 1,  
poin = $nilai_baru  
WHERE id=$id ";


// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>