<?php
if (isset($_POST['btn_simpan'])) {
  foreach ($_POST['konversi'] as $id_peserta => $nilai) {
    if ($nilai > 100) {
      die(div_alert('danger', "Nilai > 100. POST Nilai = $nilai"));
    } elseif ($nilai) {
      $s = "UPDATE tb_poin SET $get_pekan = $nilai WHERE id_peserta = $id_peserta AND id_room=$id_room";
      echo "<hr>$s";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    }
  }

  # ============================================================
  # UPDATE ROOM INI SUDAH UTS / UAS
  # ============================================================
  if ($get_pekan == 'uts' || $get_pekan == 'uas') {
    $s = "UPDATE tb_room_count SET sudah_$get_pekan=1 WHERE id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  jsurl();
}
