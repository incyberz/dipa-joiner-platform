<?php
if(isset($_POST['btn_hapus_bukti'])){
  $s = "DELETE FROM tb_bukti_$jenis WHERE id=$_POST[btn_hapus_bukti]";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();
  exit;
}