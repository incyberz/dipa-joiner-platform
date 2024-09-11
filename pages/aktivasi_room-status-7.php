<?php
# ============================================================
# LOCAL PROCESSORS
# ============================================================
if (isset($_POST['btn_reset_jadwal_kelas'])) {
  $s = "SELECT id as id_sesi FROM tb_sesi WHERE id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $s2 = "DELETE FROM tb_sesi_kelas WHERE id_sesi=$d[id_sesi]";
    echolog("id_sesi: $d[id_sesi] ... $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }
  jsurl('', 3000);
}


$s = "SELECT 1 FROM tb_sesi_kelas a 
JOIN tb_sesi b ON a.id_sesi=b.id 
JOIN tb_kelas c ON a.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 

WHERE b.id_room=$id_room 
AND d.id_room=$id_room 
AND d.ta = $ta 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$num_rows = mysqli_num_rows($q);
if ($num_rows) {
  $inputs = div_alert('danger', "
    Sudah ada $num_rows item jadwal kelas pada room ini, data jadwal kelas harus di-reset terlebih dahulu.
    <hr>
    <form method=post>
      <button name=btn_reset_jadwal_kelas class='btn btn-danger'>Reset Jadwal Kelas</button>
    </form>

  ");
?>
  <script>
    $(function() {
      $('#btn_aktivasi').hide();
    })
  </script>

<?php

} else {
  include 'aktivasi_room-status-7b.php';
}
