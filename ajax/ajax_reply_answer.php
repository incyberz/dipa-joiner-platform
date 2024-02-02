<?php 
include 'session_user.php';

// die('sukses');

# ================================================
# GET CRUD VARIABLES
# ================================================
$id_pertanyaan = isset($_GET['id_pertanyaan']) ? $_GET['id_pertanyaan'] : die(erid("id_pertanyaan"));if($id_pertanyaan=='')die(erid('id_pertanyaan(NULL)'));
$verif_status = isset($_GET['verif_status']) ? $_GET['verif_status'] : die(erid("verif_status"));if($verif_status=='')die(erid('verif_status(NULL)'));
$reply = isset($_GET['reply']) ? $_GET['reply'] : die(erid("reply"));if($reply=='')die(erid('reply(NULL)'));


# ================================================
# REPLY HANDLER
# ================================================
if($id_role==2){

  // update verif_status of pertanyaan
  $s =  "UPDATE tb_pertanyaan SET verif_by=$id_peserta, verif_date=CURRENT_TIMESTAMP, verif_status=$verif_status WHERE id=$id_pertanyaan ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  if($verif_status!=1){
    die('sukses');
  }else{
    // goto normal reply

  }

}elseif($id_role==1){
  // normal reply
}elseif($id_role==3){
  die('Anda login sebagai Supervisor! Terimakasih sudah mencoba reply!');
}else{
  die("Sepertinya Anda belum login. Silahkan relogin terlebih dahulu.");
}

// normal reply
$s =  "INSERT INTO tb_pertanyaan_reply 
(id_pertanyaan,id_penjawab,jawaban) VALUES 
('$id_pertanyaan','$id_peserta','$reply') ";

// die($s);
$q = mysqli_query($cn,$s) or die('Error @ajax. '.mysqli_error($cn));

// reply for jawabans
// $s = "SELECT a.* 
// FROM tb_pertanyaan_reply a 
// JOIN tb_peserta b ON a.id_penjawab=b.id 
// WHERE id_pertanyaan=$id_pertanyaan";
// $j='';
// while ($d=mysqli_fetch_assoc($q)) {
//   # code...
// }
die('sukses');
?>