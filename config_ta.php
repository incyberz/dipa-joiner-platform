<?php
# ============================================================
# TAHUN AJAR AKTIF
# ============================================================
$get_ta = $_GET['ta'] ?? '';
// $where = $get_ta ? "ta = $get_ta" : "status=1";

$s = "SELECT * FROM tb_ta WHERE ta=$ta_aktif";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$ta = mysqli_fetch_assoc($q);
$ta_aktif = $ta['ta'];
$ta_akhir = $ta['akhir_kuliah'];
$senin_pertama_kuliah = $ta['senin_pertama_kuliah'];
$ta_awal = $senin_pertama_kuliah;
$senin_pertama_sekolah = $ta['senin_pertama_sekolah'];
$akhir_sekolah = $ta['akhir_sekolah'];
