<?php
include 'session_user.php';
$dipa_master_username = $_SESSION['dipa_master_username'] ?? '';
if ($dipa_master_username != '') die('Silahkan Unlog-As-Peserta terlebih dahulu.');
if ($id_role != 2) die("Silahkan login sebagai $Trainer!");
