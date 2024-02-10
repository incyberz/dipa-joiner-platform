<?php
if(isset($_POST['btn_add_sublevel'])){
  $id_challenge = $_POST['btn_add_sublevel'];
  $nama = clean_sql($_POST['nama_sublevel']);

  $s = "SELECT 1 FROM tb_sublevel_challenge WHERE id_challenge=$id_challenge ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $no = mysqli_num_rows($q) + 1;
  
  $s = "SELECT 1 FROM tb_sublevel_challenge WHERE id_challenge=$id_challenge AND nama='$nama'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    echo div_alert('danger', "Sublevel <u>$nama</u> sudah ada pada challenge ini.");
  }else{
    $s = "INSERT INTO tb_sublevel_challenge 
    (no,id_challenge,nama) VALUES 
    ($no,$id_challenge,'$nama')";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo div_alert('success', "Tambah sublevel baru sukses.");

  }
}