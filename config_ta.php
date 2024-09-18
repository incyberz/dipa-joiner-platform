<?php
# ============================================================
# TAHUN AJAR AKTIF
# ============================================================
$get_ta = $_GET['ta'] ?? '';
$where = $get_ta ? "ta = $get_ta" : "status=1";
$s = "SELECT * FROM tb_ta WHERE $where";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$ta = $d['ta'];
$ta_awal = $d['awal'];
$ta_akhir = $d['akhir'];
$senin_pertama = $d['senin_pertama'];
