<?php
if (isset($_POST['btn_approve_all'])) {
  $post_jenis = $_POST['jenis'] ?? die('<span class=red>dibutuhkan data post_jenis pada verif process.</span>');
  echo "<div class='consolas abu f12'>approving multiple bukti $post_jenis...</div><hr>";

  $arr = explode(',', $_POST['btn_approve_all']);

  $s = "UPDATE tb_bukti_$post_jenis SET 
  tanggal_verifikasi=CURRENT_TIMESTAMP,
  verified_by = $id_peserta,
  status = 1 
  WHERE "; //id=$id_bukti
  foreach ($arr as $id_bukti) {
    if ($id_bukti) {
      echo "<div class='consolas abu f12'>updating bukti $post_jenis, id: $id_bukti...</div>";
      $s .= " id=$id_bukti OR ";
    }
  }
  $s .= 'false';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo "<div class='alert alert-success consolas abu f12 mt2'>executing all commands...success.</div>";

  jsurl('', 500);
}

if (isset($_POST['btn_approve'])) {
  $arr = explode('__', $_POST['btn_approve']);
  $status = $arr[0];
  $id_bukti = $arr[1];
  $jenis = $arr[2];

  if (!$id_bukti) die('id_bukti is missing');
  if (!$jenis) die('jenis is missing');

  if ($status == 1 || $status == -1) { // approve
    if ($status == 1) {

      $poin_tambahan = intval($_POST['poin_tambahan']);
      $apresiasi = $_POST['apresiasi'];

      $poin_tambahan = $poin_tambahan ? $poin_tambahan : 'NULL';
      $apresiasi = $apresiasi ? "'$apresiasi'" : 'NULL';

      $s = "UPDATE tb_bukti_$jenis SET 
      tanggal_verifikasi=CURRENT_TIMESTAMP,
      verified_by = $id_peserta,
      status = $status,
      poin_tambahan = $poin_tambahan,
      apresiasi = $apresiasi
      WHERE id=$id_bukti";
    } else { // status = -1 (reject)
      $alasan_reject = $_POST['alasan_reject'] ?? die('Alasan reject wajib diisi.');

      $s = "UPDATE tb_bukti_$jenis SET 
      tanggal_verifikasi=CURRENT_TIMESTAMP,
      verified_by = $id_peserta,
      status = $status,
      alasan_reject = '$alasan_reject' 
      WHERE id=$id_bukti";
    }
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    echo div_alert('success', "Update bukti $jenis sukses. | <a href='?verif'>Back</a>");
    jsurl('', 500);
    exit;
  } else {
    die(div_alert('danger', 'Invalid status at verification process.'));
  }
}
