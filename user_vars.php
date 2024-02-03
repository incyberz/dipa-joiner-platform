<?php 
if(!$tahun_ajar) die(erid('tahun_ajar at user_vars'));
if($dm) echo "<div style='height:50px'>.</div>DEBUG MODE ON<hr>";  

$today=date('Y-m-d');
$undef = '<span class="red kecil miring">undefined</span>';

# ========================================================
# SELECT DATA PESERTA
# ========================================================
$s = "SELECT 
a.id as id_peserta, 
a.*,
b.*,
(SELECT 1 FROM tb_biodata WHERE id=a.id) punya_biodata,
(SELECT nik FROM tb_biodata WHERE id=a.id) nik,
(
  SELECT p.kelas FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE q.tahun_ajar=$tahun_ajar
  AND p.id_peserta=a.id) kelas

FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.username='$username' 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)) die('Username tidak ditemukan.');
$d=mysqli_fetch_assoc($q);
$id_peserta = $d['id_peserta'];
$nama_peserta = ucwords(strtolower($d['nama']));
$no_wa = $d['no_wa'] ?? '';
$no_wa_show = !$no_wa?$undef:substr($no_wa,0,4).'***'.substr($no_wa,strlen($no_wa)-3,3);
$password = $d['password'];
$is_depas = !$password ? 1 : 0;
$status = $d['status'];
$profil_ok = $d['profil_ok'];
$kelas = $d['kelas'];
$sebagai = $d['sebagai'];
$punya_biodata = $d['punya_biodata'];
$nik = $d['nik'];


# ========================================================
# FOLDER UPLOADS HANDLER
# ========================================================
$folder_uploads = $d['folder_uploads'];
$id_role = $d['id_role'];
if(!$folder_uploads){
  # ========================================================
  # AUTO-CREATE FOLDER UPLOADS
  # ========================================================
  $a = '_'.strtolower($d['nama']);
  $a = str_replace(' ','',$a);
  $a = str_replace('.','',$a);
  $a = str_replace(',','',$a);
  $a = str_replace('\'','',$a);
  $a = str_replace('`','',$a);
  $a = substr($a,0,6).date('ymdHis');

  $folder_uploads = $a;
  $ss = "UPDATE tb_peserta set folder_uploads='$a' where username='$username'";
  $qq = mysqli_query($cn,$ss)or die("Update folder_uploads error. ".mysqli_error($cn));
}
if(!file_exists("uploads/$folder_uploads")) mkdir("uploads/$folder_uploads");