<?php
# ============================================================
# SUPER DELETE KELAS
# ============================================================
instruktur_only();
only_user('abi');



# ============================================================
# PROCESSORS 
# ============================================================
if (isset($_POST['btn_super_delete_kelas'])) {
  $ckelas = $_POST['btn_super_delete_kelas'];


  # ============================================================
  # DELETE SUB-DATA
  # ============================================================
  // delete kelas _peserta
  $s = "DELETE FROM tb_kelas_peserta WHERE kelas='$ckelas'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  // delete paket kelas
  $s = "DELETE FROM tb_paket_kelas WHERE kelas='$ckelas'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  // select $Room kelas
  $s = "SELECT id as id_room_kelas FROM tb_room_kelas WHERE kelas='$ckelas'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {

    // select tb_assign_challenge
    $s2 = "SELECT id as id_assign_challenge FROM tb_assign_challenge WHERE id_room_kelas='$d[id_room_kelas]'";
    echolog("-- $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    while ($d2 = mysqli_fetch_assoc($q2)) {
      // delete tb_bukti_challenge
      $s3 = "DELETE FROM tb_bukti_challenge WHERE id_assign_challenge='$d2[id_assign_challenge]'";
      echolog("-- -- $s3");
      $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
    }

    // delete tb_assign_challenge
    $s2 = "DELETE FROM tb_assign_challenge WHERE id_room_kelas='$d[id_room_kelas]'";
    echolog("-- $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));


    // select tb_assign_latihan
    $s2 = "SELECT id as id_assign_latihan FROM tb_assign_latihan WHERE id_room_kelas='$d[id_room_kelas]'";
    echolog("-- $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    while ($d2 = mysqli_fetch_assoc($q2)) {
      // delete tb_bukti_latihan
      $s3 = "DELETE FROM tb_bukti_latihan WHERE id_assign_latihan='$d2[id_assign_latihan]'";
      echolog("-- -- $s3");
      $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
    }

    // delete tb_assign_latihan
    $s2 = "DELETE FROM tb_assign_latihan WHERE id_room_kelas='$d[id_room_kelas]'";
    echolog("-- $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

    // delete tb_bertanya
    $s2 = "DELETE FROM tb_bertanya WHERE id_room_kelas='$d[id_room_kelas]'";
    echolog("-- $s2");
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  } // end while $Room kelas

  // delete $Room kelas
  $s = "DELETE FROM tb_room_kelas WHERE kelas='$ckelas'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  // delete tb_sesi_kelas
  $s = "DELETE FROM tb_sesi_kelas WHERE kelas='$ckelas'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  # ============================================================
  # FINAL DELETE
  # ============================================================
  echolog('<hr>FINAL DELETE');
  $s = "DELETE FROM tb_kelas WHERE kelas='$ckelas' AND ta=$ta";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('success mt4', "Kelas [ $ckelas ] berhasil dihapus | <a href='?super_delete_kelas'>Back</a>");
  exit;
}




# ============================================================
# MAIN PROCESS
# ============================================================
$get_kelas = $_GET['kelas'] ?? '';
echo set_h2('SUPER DELETE KELAS', "Tahun Ajar: [$ta] " . tahun_ajar_show($ta));

if (!$get_kelas) {
  // ambil data kelas pada ta sekarang
  $s = "SELECT a.kelas,
  (
    SELECT COUNT(1) FROM tb_kelas_peserta 
    WHERE kelas=a.kelas) jumlah_peserta 
  FROM tb_kelas a WHERE a.ta=$ta";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $tr = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $tr .= "
      <tr>
        <td>$d[kelas]</td>
        <td>$d[jumlah_peserta] $Peserta</td>
        <td>
          <form method=post>
            <button 
              class='btn btn-danger btn-sm' 
              onclick='return confirm(`SUPER DELETE kelas ini?`)' 
              name=btn_super_delete_kelas 
              value='$d[kelas]'
            >SUPER DELETE</button>
          </form>
        </td>
      </tr>
    ";
  }

  echo "<table class='table table-bordered'>$tr</table>";
}



if (0) {

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
}
