<?php
# ================================================
# SESSION SECURITY
# ================================================
include 'session_user.php';


# ================================================
# GET VARIABEL
# ================================================
$do_banned = 0;
$id_paket_war = $_GET['id_paket_war'] ?? die(erid('id_paket_war')); if($id_paket_war=='') die(erid("id_paket_war::null"));
$data = $_GET['data'] ?? die(erid('data')); if($data=='') die(erid("data::null"));

// $data = "6~~44~~luas permukaan~~0~~34~~12~~51~~2023-11-2 12:48:25~~~7~~44~~menghasilkan output yang benar~~0~~34~~12~~54~~2023-11-2 12:48:33~~~8~~44~~menghasilkan output yg keliru~~1~~114~~8~~51~~2023-11-2 12:48:38~~~9~~44~~NULL~~-1~~0~~0~~54~~2023-11-2 12:48:20~~~";
$s = "UPDATE tb_paket_war SET jawabans='$data' WHERE id=$id_paket_war"; // for debugging
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));


$rdata = explode('~~~',$data);
foreach ($rdata as $value) {
  if(strlen($value)>1){
    $rvalue = explode('~~',$value);
    $arr_data[$rvalue[0]] = [
      $rvalue[0],
      $rvalue[1],
      $rvalue[2],
      $rvalue[3],
      $rvalue[4],
      $rvalue[5],
      $rvalue[6],
      $rvalue[7]
    ]; 
  }
}

// echo '<pre>';
// var_dump($arr_data);
// echo '</pre>';


$values = '';
foreach ($arr_data as $d) {
  $id_soal = $d[0];
  $id_penjawab = $d[1];
  $jawaban = $d[2];
  $is_benar = $d[3];
  $poin_penjawab = $d[4];
  $poin_pembuat = $d[5];
  $id_pembuat = $d[6];
  $tanggal = $d[7];

  $jawaban = strtoupper($jawaban)=='NULL' ? 'NULL' : "'$jawaban'";

  $s = "SELECT id FROM tb_perang WHERE id_soal=$id_soal AND id_penjawab=$id_penjawab";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    $values.= "(
      '$id_soal',
      '$id_penjawab',
      '$id_pembuat',
      $jawaban,
      '$is_benar',
      '$poin_penjawab',
      '$poin_pembuat',
      '$tanggal'),";

    // update status soal
    if($is_benar==-1){
      // banned jika ada 5 rejecter
      $s = "SELECT 1 FROM tb_perang WHERE is_benar=-1 AND id_soal=$id_soal";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)>=4){
        // do banned
        $do_banned = 1;
        $s = "UPDATE tb_soal_pg SET id_status=-1 WHERE id=$id_soal";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

        // set 0 to passive point
        // add 200k to each rejecter
        $s = "UPDATE tb_perang SET poin_penjawab=200,poin_pembuat=0 WHERE id=$id_soal";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
        
      }


    }else{
      // verifikasi jika ada 10 penjawab
      $s = "SELECT 1 FROM tb_perang WHERE is_benar!=-1 AND id_soal=$id_soal";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)>=9){
        // do verified
        $s = "UPDATE tb_soal_pg SET id_status=1 WHERE id=$id_soal";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      }
    }
  }else{
    // jika sudah pakai fitur auto-save perang when load
    if(mysqli_num_rows($q)>1) die('Tidak boleh dua user berperang di satu soal.');
    $d = mysqli_fetch_assoc($q);
    $id_perang = $d['id'];

    $s = "UPDATE tb_perang SET 
      id_soal = '$id_soal',
      id_penjawab = '$id_penjawab',
      id_pembuat = '$id_pembuat',
      jawaban = $jawaban,
      is_benar = '$is_benar',
      poin_penjawab = '$poin_penjawab',
      poin_pembuat = '$poin_pembuat',
      tanggal = '$tanggal'      
    WHERE id='$id_perang'";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


  }
} // end foreach data



// final sql
if($values){
  $values .= '__';
  $values = str_replace(',__','',$values);
  
  $s = "INSERT INTO tb_perang (
  id_soal,
  id_penjawab,
  id_pembuat,
  jawaban,
  is_benar,
  poin_penjawab,
  poin_pembuat,
  tanggal) VALUES $values";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
}
// update paket war completed
$last_60 = date('Y-m-d H:i:s',strtotime('now') - (60*60)); // 1 jam for reupdate summary
$last_30 = date('Y-m-d H:i:s',strtotime('now') - (30*60)); // 20 menit for resuming quiz
$s = "UPDATE tb_paket_war SET is_completed=1 WHERE id_peserta='$id_peserta' AND tanggal >= '$last_30'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$s = "UPDATE tb_perang_summary SET last_update='$last_60' WHERE id='$id_peserta' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
die('sukses');
