<?php 
include 'instruktur_only.php';
include '../conn.php';

# ================================================
# GET CRUD VARIABLES
# ================================================
include 'get_crud_variables.php';
// $id_sesi = isset($_GET['id_sesi']) ? $_GET['id_sesi'] : die(erid("id_sesi"));if($id_sesi=='')die(erid('id_sesi(NULL)'));


# ================================================
# CRUD HANDLER
# ================================================
if($aksi=='tambah'){
  $s =  "INSERT INTO tb_sesi (no,id_sesi,nama) VALUES (0,$id_sesi,'NEW LATIHAN')";
}elseif($aksi=='hapus'){
  $s = "DELETE FROM tb_sesi WHERE id=$id";
}elseif($aksi=='ubah'){
  $isi_baru = (trim(strtolower($isi_baru))=='null' || trim(strtolower($isi_baru))=='') ? 'NULL' : "'$isi_baru'"; 
  $s = "UPDATE tb_sesi SET $kolom = $isi_baru WHERE id=$id";
}else{
  die("aksi $aksi belum terdapat handler.");
}

// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>