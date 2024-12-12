<?php
# ============================================================
# DATABASE CONNECTION
# ============================================================
$online_version = $_SERVER['SERVER_NAME'] == 'localhost' ? 0 : 1;

// dipa@iotikaindonesia.com
// ftpDIP@2024

if ($online_version) {
  $db_server = 'localhost';
  $db_user = 'pesc7881_insho';
  $db_pass = "hq'qC3D}+Hzj@TT";
  $db_name = 'pesc7881_dipa';

  $db_user = 'iotikain_adm';
  $db_pass = "hq'qC3D}+Hzj@sTT";
  $db_name = 'iotikain_dipa';

  $db_user = "mmcclini_admin";
  $db_pass = "MMC-Clinic2024";
  $db_name = "mmcclini_dipa";
} else {
  $db_server = 'localhost';
  $db_user = 'root';
  $db_pass = '';
  $db_name = 'db_dipa';

  $db_name = 'db_online_dipa_sep_2024';
  $db_name = 'db_online_dipa_oct_2024';
  // $db_name = 'tb_dipa_destroyed';
}

$cn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if ($cn->connect_errno) {
  echo "Error Konfigurasi# Tidak dapat terhubung ke MySQL Server :: $db_name";
  exit();
}

date_default_timezone_set("Asia/Jakarta");



function clean_sql($a)
{
  $a = trim($a);
  $a = str_replace('\'', '`', $a);
  $a = str_replace('"', '`', $a);
  $a = str_replace(';', ',', $a);
  return $a;
}
