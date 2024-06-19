<?php
# ============================================================
# MANAGE ACTIVITY UI
# ============================================================
$img_detail = img_icon('detail');

$s = "SELECT * FROM tb_assign_$jenis WHERE id=$id_assign";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $disabled_submit = '';
  $disabled_submit_info = '';
  if ($count_submiter) {
    $disabled_submit = 'disabled';
    $disabled_submit_info = div_alert('danger tengah miring bold mt2', "<span class=red>Tidak dapat lagi update karena sudah ada $count_submiter submiter pada $Jenis ini.</span>");
  }

  # ============================================================
  # GET ALL ID_ASSIGN 
  # ============================================================
  include 'activity_manage-manage_rule_assign_latihan.php';

  # ============================================================
  # MANAGE LATIHAN/CHALLENGE
  # ============================================================
  include 'activity_manage-manage_latihan_or_challenge.php';
} else {
  $form_manage_jenis = div_alert('danger', "Data Assign $jenis tidak ditemukan.");
}

echo "
  <div class='wadah gradasi-kuning'>
    $form_manage_jenis
  </div>
  <div class='wadah gradasi-kuning'>
    $manage_assign
  </div>
";
