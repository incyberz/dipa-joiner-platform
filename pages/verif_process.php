<?php
if(isset($_POST['btn_approve'])){
  $arr = explode('__',$_POST['btn_approve']);
  $status = $arr[0];
  $id_bukti = $arr[1];
  $jenis = $arr[2];

  if(!$id_bukti) die('id_bukti is missing');
  if(!$jenis) die('jenis is missing');

  if($status==1){ // approve
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
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

    echo div_alert('success',"Update bukti $jenis sukses. | <a href='?verif'>Back</a>");
    exit;


  }elseif($status==-1){ // reject

  }else{
    die(div_alert('danger','Invalid status at verification process.'));
  }
}