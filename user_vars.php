<?php
$id_role = $_SESSION['dipa_id_role'];
if ($is_login_as) $id_role = 1;
if (!$id_role) die(erid('id_role at user_vars'));
if (!$ta) die(erid('tahun_ajar at user_vars'));
if ($dm) echo "<div style='height:50px'>.</div>DEBUG MODE ON<hr>";

$today = date('Y-m-d');
$undef = '<span class="red kecil miring">undefined</span>';

# ========================================================
# SELECT DATA PESERTA
# ========================================================
$sql_ta = $id_role == 2 ? 1 : "q.tahun_ajar=$ta";
$s = "SELECT 
a.id as id_peserta, 
a.*,
b.*,
(SELECT 1 FROM tb_biodata WHERE id=a.id) punya_biodata,
(SELECT nik FROM tb_biodata WHERE id=a.id) nik,
(
  SELECT p.kelas FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  WHERE $sql_ta
  AND p.id_peserta=a.id) kelas

FROM tb_peserta a 
JOIN tb_role b ON a.id_role=b.id 
WHERE a.username='$username' 
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die('Username tidak ditemukan.');
$d_peserta = mysqli_fetch_assoc($q);
$id_peserta = $d_peserta['id_peserta'];
$nama_peserta = ucwords(strtolower($d_peserta['nama']));
$no_wa = $d_peserta['no_wa'] ?? '';
$no_wa_show = !$no_wa ? $undef : substr($no_wa, 0, 4) . '***' . substr($no_wa, strlen($no_wa) - 3, 3);
$password = $d_peserta['password'];
$is_depas = !$password ? 1 : 0;
$status = $d_peserta['status'];
$profil_ok = $d_peserta['profil_ok'];
$kelas = $d_peserta['kelas'];
$sebagai = $d_peserta['sebagai'];
$punya_biodata = $d_peserta['punya_biodata'];
$nik = $d_peserta['nik'];
$kelas_show = str_replace("~$ta", '', $kelas);
$Sebagai = ucwords($sebagai);
$image = $d_peserta['image'];

$war_image = $d_peserta['war_image'];
$war_image = $war_image ? $war_image : $image;


# =========================================
# AUTO INSERT BLANKO BIODATA
# =========================================
if (!$punya_biodata) {
  $s = "INSERT INTO tb_biodata (id) VALUES ($id_peserta)";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}

# ========================================================
# FOLDER UPLOADS HANDLER
# ========================================================
$folder_uploads = $d_peserta['folder_uploads'];
if (!$folder_uploads) {
  # ========================================================
  # AUTO-CREATE FOLDER UPLOADS
  # ========================================================
  $a = '_' . strtolower($d_peserta['nama']);
  $a = str_replace(' ', '', $a);
  $a = str_replace('.', '', $a);
  $a = str_replace(',', '', $a);
  $a = str_replace('\'', '', $a);
  $a = str_replace('`', '', $a);
  $a = substr($a, 0, 6) . date('ymdHis');

  $folder_uploads = $a;
  $ss = "UPDATE tb_peserta set folder_uploads='$a' where username='$username'";
  $qq = mysqli_query($cn, $ss) or die("Update folder_uploads error. " . mysqli_error($cn));
}
if (!file_exists("uploads/$folder_uploads")) mkdir("uploads/$folder_uploads");

# ========================================================
# PROFILE FOTO
# ========================================================
$rand = rand(1, 5);
$src_profil_na = "assets/img/no_profile$rand.jpg";
$punya_profil = false;
$src_profil = $src_profil_na;

if ($d_peserta['image']) {
  $src = "$lokasi_profil/$d_peserta[image]";
  if (file_exists($src)) {
    $src_profil = $src;
    $punya_profil = true;
  }
}



# ========================================================
# PROFIL PERANG
# ========================================================
$punya_profil_perang = false;
$src_profil_perang_na = "assets/img/no_war_profil.jpg";
$src_profil_perang = $src_profil_perang_na;
if ($d_peserta['war_image']) {
  $src = "$lokasi_profil/$d_peserta[war_image]";
  if (file_exists($src)) {
    $src_profil_perang = $src;
    $punya_profil_perang = true;
  }
}
