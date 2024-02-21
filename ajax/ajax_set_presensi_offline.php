<?php 
include 'instruktur_only.php';

# ================================================
# GET VERIF VARIABLES
# ================================================
$id_peserta = $_GET['id_peserta'] ?? die(erid("id_peserta"));if(!$id_peserta)die(erid('id_peserta(NULL)'));
$id_sesi = $_GET['id_sesi'] ?? die(erid("id_sesi"));if(!$id_sesi)die(erid('id_sesi(NULL)'));
$kode_absen = $_GET['kode_absen'] ?? die(erid("kode_absen"));
$poin = $_GET['poin'] ?? die(erid("poin"));

$poin = $poin ? $poin : 'NULL';

# ================================================
# CRUD HANDLER
# ================================================
$WHERE_params = "WHERE id_sesi=$id_sesi AND id_peserta=$id_peserta";
$s = "SELECT 1 FROM tb_presensi_offline $WHERE_params";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){
  $s = "UPDATE tb_presensi_offline SET 
  kode_absen=$kode_absen, 
  poin=$poin,
  tanggal=CURRENT_TIMESTAMP  
  $WHERE_params";
}else{
  $s = "INSERT INTO tb_presensi_offline 
  (id_peserta,id_sesi,kode_absen,poin) VALUES 
  ($id_peserta,$id_sesi,$kode_absen,$poin)";
}
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
echo 'sukses';
