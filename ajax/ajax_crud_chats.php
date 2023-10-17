<?php 
include 'instruktur_only.php';
include '../conn.php';

# ================================================
# GET CRUD VARIABLES
# ================================================
include 'get_crud_variables.php';
$tabel = isset($_GET['tabel']) ? $_GET['tabel'] : die(erid("tabel"));if($tabel=='')die(erid('tabel(NULL)'));


# ================================================
# CRUD HANDLER
# ================================================
if($aksi=='tambah'){
  $s =  "INSERT INTO tb_sesizzz (no,id_sesi,nama) VALUES (0,$id_sesi,'NEW LATIHAN')";
}else if($aksi=='hapus'){
  if($tabel=='pertanyaan'){
    $s = "DELETE FROM tb_jawaban WHERE id_pertanyaan=$id";
    // $q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
  }
  $s = "DELETE FROM tb_$tabel WHERE id=$id";
}elseif($aksi=='ubah'){
  $isi_baru = (trim(strtolower($isi_baru))=='null' || trim(strtolower($isi_baru))=='') ? 'NULL' : "'$isi_baru'"; 
  $s = "UPDATE tb_sesizzz SET $kolom = $isi_baru WHERE id=$id";
}else{
  die("aksi $aksi belum terdapat handler.");
}

// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));
die('sukses');
?>