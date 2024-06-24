<?php
if (isset($_POST['btn_upload'])) {
  $new_file = $_POST['btn_upload'] ?? '';
  # ============================================================
  # SERVER VALIDATION :: D_ASSIGN ALREADY EXIST
  # ============================================================

  // $no_jenis = $d_assign['no'];
  $basic_point = $d_assign['basic_point'];
  $ontime_point = $d_assign['ontime_point'];
  $ontime_dalam = $d_assign['ontime_dalam'];
  $ontime_deadline = $d_assign['ontime_deadline'];

  $tanggal_jenis = $d_assign['tanggal_assign'];
  $selisih = strtotime('now') - strtotime($tanggal_jenis);

  $sisa_ontime_point = 0;
  if ($selisih < $ontime_dalam * 60) {
    echolog("add with ontime_point: $ontime_point");
    $get_point = $basic_point + $ontime_point;
  } else if ($selisih > $ontime_dalam * 60 + $ontime_deadline * 60) {
    echolog("only basic_point: $basic_point, selisih: $selisih second");
    $get_point = $basic_point;
  } else {
    // echo 'if3<br>';
    $telat_point = round((($selisih - $ontime_dalam * 60) / ($ontime_deadline * 60)) * $ontime_point, 0);
    echolog("decrease with telat_point: $telat_point");
    $sisa_ontime_point = $ontime_point - $telat_point;
    $get_point = $basic_point + $sisa_ontime_point;
  }

  $multiplier = $basic_point;
  $multiplier += $jenis == 'latihan' ? 0 : $ontime_point;
  $poin_antrian = $arr_persen_poin_antrian[$count_submiter] * $multiplier / 100;

  # ============================================================
  # IDEMPOTENCE DUPLICATE CHECK
  # ============================================================
  $s = "SELECT id as id_bukti FROM tb_bukti_$jenis WHERE id_assign_$jenis=$id_assign_jenis and id_peserta=$id_peserta";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) == 0) {
    $kolom_link = $jenis == 'challenge' ? ',link' : '';
    $link_value = $jenis == 'challenge' ? ",'$_POST[bukti]'" : '';
    $s = "INSERT INTO tb_bukti_$jenis (
      id_assign_$jenis,
      id_peserta,
      poin_antrian,      
      image,
      get_point$kolom_link
    ) VALUES (
    $id_assign_jenis,
    $id_peserta,
    $poin_antrian,
    '$new_file',
    $get_point$link_value
    )";
    $pesan_upload = div_alert('success', "Upload success. Tunggulah hingga instruktur melakukan verifikasi bukti $jenis kamu!");
    // die("<pre>$s</pre>");

  } else {
    $set_link = $jenis == 'challenge' ? "link = '$_POST[bukti]'" : '';
    $d_bukti = mysqli_fetch_assoc($q);
    $id_bukti = $d_bukti['id_bukti'];
    $s = "UPDATE tb_bukti_$jenis SET 
    $set_link 
    get_point = '$get_point',
    poin_antrian = '$poin_antrian',
    image = '$new_file',
    tanggal_upload = CURRENT_TIMESTAMP 
    WHERE id=$id_bukti
    ";
    $pesan_upload = div_alert('info', 'Kamu sudah upload bukti sebelumnya, replace berhasil.');
  }

  # ============================================================
  # FILE HANDLER BUKTI LATIHAN
  # ============================================================

  echolog("new_file: $new_file");
  $target_bukti = "uploads/$folder_uploads/$new_file";
  echolog("executing: $s");

  if (isset($_FILES['bukti'])) {
    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_bukti) && $jenis != 'challenge') {
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      // echo $pesan_upload;
      // echo "<script>location.replace('?activity&jenis=$jenis&no=$no_jenis')</script>";
      // jsurl();
      // exit;
    } else {
      echo div_alert('danger', 'Tidak dapat move_uploaded_file.');
    }
  } else {
    // for challenge
    // tanpa upload file
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  echo "$pesan_upload<hr><a href='?activity&jenis=$jenis&id_assign=$id_assign'>Back</a>";
  jsurl('', 3000);
  exit;
}



# ============================================================
# HAPUS BUKTI
# ============================================================
if (isset($_POST['btn_hapus_bukti'])) {
  $s = "DELETE FROM tb_bukti_$jenis WHERE id=$_POST[btn_hapus_bukti]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
  exit;
}


# ============================================================
# ADD SUB LEVEL
# ============================================================
if (isset($_POST['btn_add_sublevel'])) {
  $id_challenge = $_POST['btn_add_sublevel'];
  $nama = clean_sql($_POST['nama_sublevel']);

  $s = "SELECT 1 FROM tb_sublevel_challenge WHERE id_challenge=$id_challenge ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $no = mysqli_num_rows($q) + 1;

  $s = "SELECT 1 FROM tb_sublevel_challenge WHERE id_challenge=$id_challenge AND nama='$nama'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Sublevel <u>$nama</u> sudah ada pada challenge ini.");
  } else {
    $s = "INSERT INTO tb_sublevel_challenge 
    (no,id_challenge,nama) VALUES 
    ($no,$id_challenge,'$nama')";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Tambah sublevel baru sukses.");
  }
}
