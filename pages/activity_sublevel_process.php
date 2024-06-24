<?php
if (isset($_POST['btn_submit_link'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  $arr = explode('__', $_POST['btn_submit_link']);

  $id_sublevel = $arr[0];
  $get_point = $arr[1];
  $link = $_POST['bukti_link'];

  if (!$id_sublevel) die('id_sublevel is null at activity sublevel process.');
  if (!$get_point) die('id_sublevel is null at activity sublevel process.');
  if (!$link) die('id_sublevel is null at activity sublevel process.');


  $s = "INSERT INTO tb_bukti_challenge 
  (id_assign_challenge,get_point,id_peserta,link,id_sublevel) VALUES 
  ($id_assign,$get_point,$id_peserta,'$link',$id_sublevel)
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  jsurl();
  exit;
}

if (isset($_POST['btn_update_sublevel'])) {

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
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  jsurl();
  exit;
}
