<?php
if(!$username) jsurl('?');
if(!$status_room) die("<section><div class=container>$div_alert_closed</div></section>");
$jenis = $_GET['jenis'] ?? '';
$id_assign = $_GET['id_assign'] ?? '';

// bukti latihan
$target_bukti = "uploads/$folder_uploads/$jenis-$id_assign.jpg";

if($jenis==''){
  $rjenis = ['latihan','challenge'];
  $j='';
  foreach ($rjenis as $key => $value) $j .= "<a href='?activity&jenis=$value data-aos='fade-up'' class='proper btn btn-info mb2'>$value</a> ";
  echo "<section><div class=container><div data-aos='fade-up'><p>Silahkan pilih jenis aktivitas:</p>$j</div></div></section>";
  exit;
}

$ryaitu = [
  'latihan' => 'Yaitu praktikum yang persis dicontohkan oleh instruktur atau materi yang sudah disampaikan. Kamu wajib mengerjakannya.',
  'challenge' => 'Yaitu pembuktian bahwa kamu sudah siap terjun ke Dunia Usaha dan Industri (DUDI). Kamu wajib membangun salah satu portfolio system yang berhasil kamu buat.'
];
$yaitu = $ryaitu[$jenis];
$pesan_upload = null;

# ============================================
# NORMAL FLOW
# ============================================
if(!$id_assign){

  $s = "SELECT a.nama,
  (
    SELECT 1 FROM tb_assign_$jenis
    WHERE id_$jenis=a.id 
    AND id_room_kelas=$id_room_kelas) assigned 
  FROM tb_$jenis a 
  WHERE a.id_room=$id_room";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $count_jenis = mysqli_num_rows($q);

  $list = '';
  while($d=mysqli_fetch_assoc($q)){
    if($d['assigned']) continue;
    $list.="<li>$d[nama]</li>";
  }
  echo $list ? "<div class='wadah gradasi-kuning'>List $jenis yang belum bisa dikerjakan: <ol>$list</ol><div class='f12 biru miring'>Hubungi instruktur agar $jenis ini di-assign ke kelas kamu.</div></div>" : '';

  $s = "SELECT a.id as id_assign, 
  b.nama,
  (b.basic_point + b.ontime_point) as sum_point,
  c.no, 
  (
    SELECT 1 FROM tb_bukti_$jenis 
    WHERE id_assign_$jenis=a.id 
    AND id_peserta=$id_peserta) sudah_mengerjakan,   
  (
    SELECT status FROM tb_bukti_$jenis 
    WHERE id_assign_$jenis=a.id 
    AND id_peserta=$id_peserta) status_mengerjakan   
  FROM tb_assign_$jenis a 
  JOIN tb_$jenis b ON a.id_$jenis=b.id 
  JOIN tb_sesi c ON a.id_sesi=c.id 
  -- WHERE no is not null 
  WHERE 1  
  AND id_room_kelas='$id_room_kelas'
  order by c.no, sum_point";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(!mysqli_num_rows($q)){
    if($count_jenis){
      echo div_alert('danger',"Terdapat $count_jenis $jenis yang belum di-assign oleh instruktur untuk kelas $kelas");
    }else{
      echo div_alert('danger',"Maaf, belum ada satupun $jenis pada room $room. Beritahukan hal ini kepada instruktur!");
    }

  }else{
    $rno = '';
    while ($d=mysqli_fetch_assoc($q)) {
      $primary = $d['sudah_mengerjakan'] ? 'warning' : 'primary';
      $primary = $d['status_mengerjakan'] ? 'success' : $primary;
      $sum_point = number_format($d['sum_point'],0);
      $rno .= "
        <div>
          <a class='btn btn-$primary btn-sm mb2' href='?activity&jenis=$jenis&id_assign=$d[id_assign]'>
            P$d[no] 
            ~ 
            $d[nama]
            ~ 
            $sum_point
          </a>
        </div>
      ";
    }
    echo "
      Silahkan pilih $jenis yang dapat kamu kerjakan:
      <div class=wadah>
        $rno
      </div>
      <div class='kecil miring'>
        <span class=hijau>hijau: sudah dikerjakan</span>; <span class=kuning>kuning: belum diverifikasi</span>; <span class=biru>biru: belum kamu kerjakan</span>
      </div>
    ";
  }

}else{
  include 'activity_show.php';
}


if($id_role==2){
  if(!$id_assign){
    include 'activity_assign.php';
  } 
}

echo "<div class=debug>
  jenis: <span id=jenis>$jenis</span>
  id_assign: <span id=id_assign>$id_assign</span>
</div>";