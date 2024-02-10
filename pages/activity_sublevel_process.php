<?php
if(isset($_POST['btn_submit_link'])){
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $get_point = $_POST['btn_submit_link'];
  $link = $_POST['bukti_link'];


  $s = "INSERT INTO tb_bukti_challenge 
  (id_assign_challenge,get_point,id_peserta,link) VALUES 
  ($id_assign,$get_point,$id_peserta,'$link')
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl();
  exit;
}

if(isset($_POST['btn_update_sublevel'])){
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $id_sublevel = $_POST['btn_update_sublevel'];
  $nama_sublevel = clean_sql($_POST['nama_sublevel']);
  $objective = clean_sql($_POST['objective']);
  $poin_sublevel = clean_sql($_POST['poin_sublevel']);


  $s = "UPDATE tb_sublevel_challenge SET 
  nama='$nama_sublevel', 
  poin='$poin_sublevel', 
  objective='$objective'
  WHERE id=$id_sublevel
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl();
  exit;
}

