<?php
session_start();

if(!isset($_SESSION['dipa_username']) || !isset($_SESSION['dipa_role'])) die('Silahkan login terlebih dahulu!');
$username = $_SESSION['dipa_username'];
$id_role = $_SESSION['dipa_role'];
$id_peserta = $_SESSION['dipa_id_peserta'];