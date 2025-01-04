<h1>Super Delete Latihan</h1>
<?php
$get_kelas = $_GET['kelas'];


exit;
$id_latihan = 114;
echo "<div class='consolas  '>";

$s = "SELECT id as id_assign_latihan FROM tb_assign_latihan WHERE id_latihan=$id_latihan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo "<br>executing: $s";
while ($d = mysqli_fetch_assoc($q)) {
  $s2 = "SELECT id as id_bukti_latihan FROM tb_bukti_latihan WHERE id_assign_latihan=$d[id_assign_latihan]";
  echo "<br>-- loop executing: $s2";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  while ($d2 = mysqli_fetch_assoc($q2)) {

    $s3 = "DELETE FROM tb_bukti_latihan WHERE id = $d2[id_bukti_latihan]";
    echo "<br>-- -- loop executing: $s3";
    $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
  }


  $s2 = "DELETE FROM tb_assign_latihan WHERE id = $d[id_assign_latihan]";
  echo "<br>-- loop executing: $s2";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
}

// GOAL
$s = "DELETE FROM tb_latihan WHERE id = $id_latihan";
echo "<br>FINAL executing: $s";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo "<br>FINAL executing: success";
echo "</div>";
