<?php
$s = "SELECT a.*, 
a.id as id_sublevel,
a.nama as nama_sublevel
FROM tb_sublevel_challenge a WHERE 1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger', "Belum ada data sublevel.");
}else{
  // $div = '';
  while($d=mysqli_fetch_assoc($q)){
    // $id=$d['id'];
    // $div .= "<div>$d[nama_sesi]</div>";
  }
}