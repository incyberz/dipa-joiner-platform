<?php
session_start();
require_once '../conn.php';

if (isset($_SESSION['lms_username'])) {
  echo 'Anda sedang login';
  exit;
}

if (!isset($_GET['username']) || trim($_GET['username']) === '') {
  echo 'Username tidak boleh kosong';
  exit;
}

$username = trim($_GET['username']);

// Amankan query dengan prepared statement
$stmt = $cn->prepare("SELECT COUNT(*) FROM tb_peserta WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count == 0) {
  echo 'OK';
} else {
  echo "Username [$username] tidak tersedia.";
}
