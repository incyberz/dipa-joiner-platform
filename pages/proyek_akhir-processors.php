<?php
if (isset($_POST['btn_upload'])) {
  $id_indikator = $_POST['btn_upload'];

  foreach ($_FILES as $indikator => $arr) {
    $tmp_name = $arr['tmp_name'];
    $date = date('YmdHis');
    $unique = "$id_peserta-$id_room-$indikator";
    $new_name = "$username-$indikator-$date.jpg";
    $target_path = "$lokasi_proyek/$new_name";
    if (move_uploaded_file($tmp_name, $target_path)) {

      # ============================================================
      # DELETE OLD FILE
      # ============================================================
      $s = "SELECT bukti FROM tb_bukti_proyek WHERE kode = '$unique'";
      echolog($s);
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $d = mysqli_fetch_assoc($q);
      $bukti_lama = $d['bukti'] ?? null;
      if ($bukti_lama) {
        echolog('UNLINK OLD FILE');
        unlink("$lokasi_proyek/$bukti_lama");
        unlink("$lokasi_proyek/thumb-$bukti_lama");
      }


      include_once 'includes/resize_img.php';
      # ============================================================
      echolog('RESIZE IMAGE IF NECESSARY');
      # ============================================================
      resize_img($target_path);

      # ============================================================
      echolog('CREATE THUMB');
      # ============================================================
      $thumb = "$lokasi_proyek/thumb-$new_name";
      resize_img($target_path, $thumb, 100, 100);

      # ============================================================
      echolog('INSERT BUKTI PROYEK');
      # ============================================================
      $s = "INSERT INTO tb_bukti_proyek (
        kode,
        id_peserta,
        id_indikator,
        bukti
      ) VALUES (
        '$unique',
        $id_peserta,
        $id_indikator,
        '$new_name'
      ) ON DUPLICATE KEY UPDATE 
        bukti='$new_name',
        tanggal_submit=NOW(),
        verif_by = NULL,
        verif_at = NULL,
        poin = NULL
      ";
      // die($s);
      mysqli_query($cn, $s) or die(mysqli_error($cn));
      echo 'berhasil upload';
      jsurl();
    } else {
      echo "gagal upload | tmp_name: $tmp_name | lokasi_proyek/new_name: $lokasi_proyek/$new_name";
    }
  }
}
