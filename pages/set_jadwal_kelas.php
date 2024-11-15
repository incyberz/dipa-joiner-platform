<?php
$get_kelas = $_GET['kelas'];
if (!$get_kelas) die(erid('kelas'));

if (!isset($_SESSION['target_kelas']) || $_SESSION['target_kelas'] != $get_kelas) {
  $_SESSION['target_kelas'] = $get_kelas;
  jsurl();
}
jsurl("?presensi");
