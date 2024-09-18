<?php
$s = "SELECT * FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
WHERE b.kelas = 'S1-SI-B-R-SM3-20241' 
";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));


# ============================================================
# FETCH DATA 
# ============================================================
while ($d = mysqli_fetch_assoc($q)) {
  echo "<hr>$d[nama]";

  # ============================================================
  # RESET ALL PASSWORD
  # ============================================================
  $s2 = "UPDATE tb_peserta SET password = NULL WHERE id = $d[id]";
  echolog($s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
}
