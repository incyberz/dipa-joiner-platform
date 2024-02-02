<?php
// echo div_alert('info tengah', "Mode Random Selected.");
$start = $_GET['start'] ?? '';

$s = "SELECT a.id FROM tb_soal_pg a 
LEFT JOIN tb_war b ON a.id=b.id_soal AND b.id_penjawab=$id_peserta 
JOIN tb_sesi c ON a.id_sesi=c.id 
WHERE (a.id_status is null OR a.id_status >= 0) 
AND b.id is null 
AND a.id_pembuat!=$id_peserta 
AND c.id_room=$id_room 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$available_soal = mysqli_num_rows($q);

// update peserta
$s = "UPDATE tb_peserta SET last_update_available_soal=CURRENT_TIMESTAMP, available_soal=$available_soal WHERE id=$id_peserta 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$max_soal = $available_soal>10 ? 10 : $available_soal;

# =====================================================================
# LOAD IDSOALS FROM PAKET WAR IF EXISTS AND < 30 MENIT
# =====================================================================
$last_20 = date('Y-m-d H:i:s',strtotime('now') - (20*60)); // 20 menit for resuming quiz

$s = "SELECT id_soals FROM tb_paket_war WHERE tanggal > '$last_20' AND is_completed is null AND id_peserta=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// echo "<pre>$s</pre>";
$arr_id_soal = [];
if(mysqli_num_rows($q)){
  // die('Masih ada');
  $d = mysqli_fetch_assoc($q);
  $id_soals = $d['id_soals'];
  $rid_soal = explode(',',$id_soals);
  foreach ($rid_soal as $id_soal) {
    if(strlen($id_soal)>0){
      array_push($arr_id_soal,$id_soal);
    }
  }
}


if(!$start){
  $link_start = !$max_soal ? "<span class='btn btn-secondary btn-block' onclick='alert(\"Suruhlah kawanmu untuk membuat soal agar kamu dapat Play Kuis!\")'>Kawanmu belum membuat soal!</span>" : "<a class='btn btn-primary btn-block' href='?perang_soal&mode=random&start=1'>Start $max_soal Quiz PG!</a>";

  echo "
  <div class='tebal tengah'>Rules!!</div>
  <ul class=darkred>
    <li class='tebal red'>Dilarang Refresh Browser!!</li>
    <li>Akan diload 1 s.d 10 soal random milik kawanmu, dan soal yang sudah muncul <span class=red>tidak bisa diload ulang</span></li>
    <li>Menjawab benar ataupun salah tetap menghasilkan poin</li>
    <li>Soal ter-reject menghasilkan 200 LP reject-poin (tertunda) dan poin negatif bagi pembuat soal. Soal terverifikasi tidak bisa di-reject</li>
  </ul>
  $link_start
  ";
}else{


  if($available_soal || count($arr_id_soal)){

    if($dm){
      echo '<pre>';
      var_dump($arr_id_soal);
      echo '</pre>';
    }
    // jika ada soal | resume quiz
    include 'perang_soal_random_started.php';
  }else{

    echo "<div class=tengah><img src='assets/img/soal_habis.png' class=img-fluid /></div>";
    $m = meme('dont-have');
    echo div_alert('danger tengah mt2', "<div class=mb2>Wah maaf, sepertinya soal PG-nya habis. Suruh kawanmu bikin ya! Atau kamu aja yang <a href='?tanam_soal'>bikin soal PG</a>.</div>$m");
  }
}