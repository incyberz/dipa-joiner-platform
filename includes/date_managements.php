<?php
# ============================================================
# DATE MANAGEMENTS v.2.0.1
# ============================================================
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$w = date('w', strtotime($today));
$week = intval(strtotime('now') / (7 * 24 * 60 * 60));

$ahad_skg = date('Y-m-d', strtotime("-$w day", strtotime($today)));
$besok = date('Y-m-d H:i', strtotime('+1 day', strtotime('today')));
$lusa = date('Y-m-d H:i', strtotime('+2 day', strtotime('today')));

$senin_skg = date('Y-m-d', strtotime("+1 day", strtotime($ahad_skg)));
$selasa_skg = date('Y-m-d', strtotime("+2 day", strtotime($ahad_skg)));
$rabu_skg = date('Y-m-d', strtotime("+3 day", strtotime($ahad_skg)));
$kamis_skg = date('Y-m-d', strtotime("+4 day", strtotime($ahad_skg)));
$jumat_skg = date('Y-m-d', strtotime("+5 day", strtotime($ahad_skg)));
$sabtu_skg = date('Y-m-d', strtotime("+6 day", strtotime($ahad_skg)));
$ahad_depan = date('Y-m-d', strtotime("+7 day", strtotime($ahad_skg)));

$senin_skg_show = 'Senin, ' . date('d M Y', strtotime($senin_skg));
$sabtu_skg_show = 'Sabtu, ' . date('d M Y', strtotime($sabtu_skg));

# ============================================================
# SELAMAT PAGI
# ============================================================
$waktu = "Pagi";
if (date("H") >= 9) $waktu = "Siang";
if (date("H") >= 15) $waktu = "Sore";
if (date("H") >= 18) $waktu = "Malam";

# ============================================================
# HARI INI
# ============================================================
$nama_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$nama_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hari_ini = $nama_hari[date('w')] . ', ' . date('d') . ' ' . $nama_bulan[intval(date('m')) - 1] . ' ' . date('Y');

include 'durasi_hari.php';
include 'eta.php';
