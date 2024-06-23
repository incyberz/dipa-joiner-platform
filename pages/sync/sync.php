<?php
$img_check = img_icon('check');
$img_prev = img_icon('prev');
$img_next = img_icon('next');
$img_gray = img_icon('gray');
$img_reject = img_icon('reject');

$aksi = $_GET['aksi'] ?? '';
$link_home = "<a href='?sync'>$img_prev</a>";

# ============================================================
# CONNECTIONS
# ============================================================
$db_name1 = 'db_online_dipa_mei_2024';
$db_name2 = 'db_online_dipa_juni_2024';
$cn1 = mysqli_connect('localhost', 'root', '', $db_name1);
$cn2 = mysqli_connect('localhost', 'root', '', $db_name2);

if (!$aksi) {
  # ============================================================
  # SHOW TABLES
  # ============================================================
  include 'show_tables.php';
} else {
  include "sync-$aksi.php";
}
