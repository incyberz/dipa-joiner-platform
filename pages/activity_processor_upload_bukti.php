<?php
if(isset($_POST['btn_upload'])){

  $id_assign_jenis = $_POST['btn_upload'];
  $s = "SELECT a.*,b.*  
  FROM tb_assign_$jenis a 
  JOIN tb_$jenis b ON a.id_$jenis=b.id 
  WHERE a.id=$id_assign_jenis";
    echo "<pre>$s</pre>";

  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $no_jenis = $d['no'];
  $tanggal_jenis = $d['tanggal'];
  $basic_point = $d['basic_point'];
  $ontime_point = $d['ontime_point'];
  $ontime_dalam = $d['ontime_dalam'];
  $ontime_deadline = $d['ontime_deadline'];

  $selisih = strtotime('now')-strtotime($tanggal_jenis);

  $sisa_ontime_point=0;
  if($selisih<$ontime_dalam*60){
    $get_point = $basic_point + $ontime_point;
  }else if($selisih > $ontime_dalam*60 + $ontime_deadline*60){
    $get_point = $basic_point;
  }else{
    // echo 'if3<br>';
    $telat_point = round((($selisih-$ontime_dalam*60)/($ontime_deadline*60))*$ontime_point,0);
    $sisa_ontime_point = $ontime_point - $telat_point;
    $get_point = $basic_point + $sisa_ontime_point;
  }

  $s = "SELECT id as id_bukti FROM tb_bukti_$jenis WHERE id_assign_$jenis=$id_assign_jenis and id_peserta=$id_peserta";
      // echo "<pre>$s</pre>";

  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    $kolom_link = $jenis=='challenge' ? ',link' : '';
    $link_value = $jenis=='challenge' ? ",'$_POST[bukti]'" : '';
    $s = "INSERT INTO tb_bukti_$jenis (id_assign_$jenis,id_peserta,get_point$kolom_link) VALUES ($id_assign_jenis,$id_peserta,$get_point$link_value)";
    $pesan_upload = div_alert('success',"Upload success. Tunggulah hingga instruktur melakukan verifikasi bukti $jenis kamu!");
    // die("<pre>$s</pre>");
    
  }else{
    $set_link = $jenis=='challenge' ? "link = '$_POST[bukti]'" : '';
    $d = mysqli_fetch_assoc($q);
    $id_bukti = $d['id_bukti'];
    $s = "UPDATE tb_bukti_$jenis SET 
    $set_link 
    get_point = '$get_point',
    tanggal_upload = CURRENT_TIMESTAMP 
    WHERE id=$id_bukti
    ";
    $pesan_upload = div_alert('info','Kamu sudah upload bukti sebelumnya, replace berhasil.');
  }  

  if(isset($_FILES['bukti'])){
    if(move_uploaded_file($_FILES['bukti']['tmp_name'],$target_bukti) && $jenis!='challenge'){
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      echo $pesan_upload;
      // echo "<script>location.replace('?activity&jenis=$jenis&no=$no_jenis')</script>";
      jsurl();
      exit;
    }else{
      $pesan_upload = div_alert('danger','Tidak dapat move_uploaded_file.');
    }    
  }else{
    // for challenge
    // tanpa upload file
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo $pesan_upload;
    // echo "<script>location.replace('?activity&jenis=$jenis&no=$no_jenis')</script>";
    jsurl();
    exit;
  }

}