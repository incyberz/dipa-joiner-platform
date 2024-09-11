<?php
# ============================================================
# TAHUN AJAR AKTIF
# ============================================================
$s = "SELECT * FROM tb_ta WHERE status=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$ta = $d['ta'];
$ta_awal = $d['awal'];
$ta_akhir = $d['akhir'];
$senin_pertama = $d['senin_pertama'];
