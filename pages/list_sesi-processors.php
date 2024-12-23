<?php
# ============================================================
# MOVE UP SESI 
# ============================================================
if (isset($_POST['btn_move_up'])) {
  $id = $_POST['btn_move_up'];

  echo div_alert('danger', "Maaf, fitur [move-up-urutan-sesi] sedang dalam tahap pengembangan. Mohon hubungi developer untuk info lebih lanjut!");
  jsurl('', 3000);
}


# ============================================================
# ADD SESI HANDLER
# ============================================================
$s = "SELECT 1 FROM tb_sesi WHERE id_room=$id_room";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_count_sesi = mysqli_num_rows($q);
if (isset($_POST['btn_add_sesi'])) {
  $new_count_sesi = $total_count_sesi + 1;
  echo div_alert('danger', "Maaf, fitur [add-sesi] sedang dalam tahap pengembangan. Mohon hubungi developer untuk info lebih lanjut!");
  jsurl('', 3000);

  $s = "INSERT INTO tb_sesi (id_room,no,nama) VALUES ($id_room,$new_count_sesi,'NEW SESI $new_count_sesi')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Sesi Baru berhasil dibuat.');
  jsurl();
  exit;
}
