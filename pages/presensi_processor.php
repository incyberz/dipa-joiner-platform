<?php
if (isset($_POST['btn_update_jadwal_kelas'])) {
  echo '<br>updating schedule...<hr>';

  $jadwal_kelas = "$_POST[tanggal_sesi] $_POST[jam_sesi]";
  $arr = explode('__', $_POST['btn_update_jadwal_kelas']);
  $cid_sesi = $arr[0];
  $ckelas = $arr[1];
  $id_sesi_and_kelas = "id_sesi=$arr[0] AND kelas='$arr[1]'";

  $s = "SELECT 1 FROM tb_sesi_kelas WHERE $id_sesi_and_kelas";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $s = "UPDATE tb_sesi_kelas SET jadwal_kelas='$jadwal_kelas' WHERE $id_sesi_and_kelas";
  } else {
    $s = "INSERT INTO tb_sesi_kelas (id_sesi,kelas,jadwal_kelas) VALUES ($arr[0],'$arr[1]','$jadwal_kelas')";
  }
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  if (isset($_POST['update_next_week'])) {
    echo '<br>updating next week schedule...';

    // get no of this sesi
    $s = "SELECT no as no_sesi FROM tb_sesi WHERE id=$cid_sesi";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $no_sesi = $d['no_sesi'];
    echo "<br>get no of this sesi... no_sesi: $no_sesi";

    // get all next sesi
    $s = "SELECT id as id_sesi, nama as nama_sesi,no,jenis 
    FROM tb_sesi WHERE no>$no_sesi AND id_room=$id_room 
    ORDER BY no";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo "<br>get all next sesi... sesi count: " . mysqli_num_rows($q);
    $i = 0;
    while ($d2 = mysqli_fetch_assoc($q)) {
      $i++;
      $s2 = "SELECT 1 FROM tb_sesi_kelas WHERE id_sesi=$d2[id_sesi] AND kelas='$ckelas'";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

      $durasi = $i * 7;
      $new_jadwal_kelas = date('Y-m-d H:i', strtotime("+$durasi day", strtotime($jadwal_kelas)));


      if (mysqli_num_rows($q2)) {
        $s2 = "UPDATE tb_sesi_kelas SET jadwal_kelas='$new_jadwal_kelas' WHERE id_sesi=$d2[id_sesi] AND kelas='$ckelas'";
      } else {
        $s2 = "INSERT INTO tb_sesi_kelas (id_sesi,kelas,jadwal_kelas) VALUES ($d2[id_sesi],'$ckelas','$new_jadwal_kelas')";
      }
      echo "<br>updating Sesi-$d2[no] - <span class='darkblue'>[ $d2[nama_sesi] ]</span> - tipe $d2[jenis]<div class=f10>executing... $s2</div>";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }
  }
  echo ("All command... success | <a href='?presensi'>Back to Presensi</a>");
  // jsurl();
  exit;
}

if (isset($_POST['btn_update_durasi_presensi'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  echo '<br>updating durasi presensi...<hr>';

  $cid_sesi = $_POST['btn_update_durasi_presensi'];
  $awal_presensi = $_POST['awal_presensi'];
  $akhir_presensi = $_POST['akhir_presensi'];
  $s = "UPDATE tb_sesi SET 
  awal_presensi='$awal_presensi' ,
  akhir_presensi='$akhir_presensi' 
  WHERE id=$cid_sesi";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // echo $s;

  if (isset($_POST['update_next_week'])) {
    echo '<br>updating next week duration-schedule...';

    // get no of this sesi
    $s = "SELECT no as no_sesi FROM tb_sesi WHERE id=$cid_sesi";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $no_sesi = $d['no_sesi'];
    echo "<br>get no of this sesi... no_sesi: $no_sesi";

    // get all next sesi
    $s = "SELECT id as id_sesi, nama as nama_sesi,no,jenis 
    FROM tb_sesi WHERE no>$no_sesi AND id_room=$id_room 
    ORDER BY no";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo "<br>get all next sesi... sesi count: " . mysqli_num_rows($q);
    $i = 0;
    while ($d2 = mysqli_fetch_assoc($q)) {
      $i++;
      $durasi = $i * 7;
      $new_awal_presensi = date('Y-m-d H:i', strtotime("+$durasi day", strtotime($awal_presensi)));
      $new_akhir_presensi = date('Y-m-d H:i', strtotime("+$durasi day", strtotime($akhir_presensi)));

      $s2 = "UPDATE tb_sesi SET 
      awal_presensi='$new_awal_presensi' ,
      akhir_presensi='$new_akhir_presensi' 
      WHERE id=$d2[id_sesi] ";

      echo "<br>updating Sesi-$d2[no] - <span class='darkblue'>[ $d2[nama_sesi] ]</span> - tipe $d2[jenis]<div class=f10>executing... $s2</div>";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }
  }

  echo "<hr>All command... success | <a href='?presensi'>Back to Presensi</a>";
  // jsurl();
  exit;
}
