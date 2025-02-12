<?php
# ============================================================
# TAHUN AJAR AKTIF
# ============================================================
$get_ta = $_GET['ta'] ?? '';
// $where = $get_ta ? "ta = $get_ta" : "status=1";

$s = "SELECT * FROM tb_ta WHERE ta=$ta_aktif";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$ta = $d['ta'];
$ta_akhir = $d['akhir_kuliah'];
$senin_pertama_kuliah = $d['senin_pertama_kuliah'];
$ta_awal = $senin_pertama_kuliah;
$senin_pertama_sekolah = $d['senin_pertama_sekolah'];
$akhir_sekolah = $d['akhir_sekolah'];
