<?php
session_start();

if(!isset($_SESSION['dipa_username']) || !isset($_SESSION['dipa_id_role'])) die('Silahkan login terlebih dahulu!');
$username = $_SESSION['dipa_username'];
$id_role = $_SESSION['dipa_id_role'];
$id_peserta = $_SESSION['dipa_id_peserta'];

$id_room = 1; /// zzz debug

include '../conn.php';