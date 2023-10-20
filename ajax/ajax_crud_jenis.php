<?php 
include 'instruktur_only.php';
include '../conn.php';

# ================================================
# GET CRUD VARIABLES
# ================================================
include 'get_crud_variables.php';
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : die(erid("jenis"));if($jenis=='')die(erid('jenis(NULL)'));
$id_sesi = isset($_GET['id_sesi']) ? $_GET['id_sesi'] : die(erid("id_sesi"));if($id_sesi=='')die(erid('id_sesi(NULL)'));


# ================================================
# CRUD HANDLER
# ================================================
if($aksi=='tambah'){
  $s =  "INSERT INTO tb_assign_$jenis (no,id_sesi,nama) VALUES (0,$id_sesi,'NEW LATIHAN')";
}elseif($aksi=='hapus'){
  $s = "DELETE FROM tb_assign_$jenis WHERE id=$id";
}elseif($aksi=='ubah'){
  $isi_baru = (trim(strtolower($isi_baru))=='null' || trim(strtolower($isi_baru))=='') ? 'NULL' : "'$isi_baru'"; 
  if(substr($kolom,0,8)=='tanggal_') $kolom = 'tanggal';
  $s = "UPDATE tb_assign_$jenis SET $kolom = $isi_baru WHERE id=$id";
}else{
  die("aksi $aksi belum terdapat handler.");
}

// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>