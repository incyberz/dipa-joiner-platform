<?php
# ============================================================
# DATABASE CONNECTION
# ============================================================
$is_live = $_SERVER['SERVER_NAME'] == 'localhost' ? 0 : 1;

// dipa@iotikaindonesia.com
// ftpDIP@2024

$db_server = 'localhost';
if ($is_live) {
  $db_user = "mmcclini_admin";
  $db_pass = "MMC-Clinic2024";
  $db_name = "mmcclini_dipa";
} else {
  $db_user = 'root';
  $db_pass = '';
  $db_name = 'db_dipa_v5';
}

$cn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if ($cn->connect_errno) {
  echo "Error Konfigurasi# Tidak dapat terhubung ke MySQL Server :: $db_name";
  exit();
}

date_default_timezone_set("Asia/Jakarta");
