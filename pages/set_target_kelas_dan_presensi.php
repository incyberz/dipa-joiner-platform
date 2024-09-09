<?php
$kelas = $_GET['kelas'] ?? die(erid('kelas'));
$_SESSION['target_kelas'] = $kelas;
jsurl("?presensi_rekap&kelas=$kelas");
