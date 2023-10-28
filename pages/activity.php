<?php
$jenis = $_GET['jenis'] ?? '';
$no = $_GET['no'] ?? '';

if($jenis==''){
  $rjenis = ['latihan','tugas','challenge'];
  $j='';
  foreach ($rjenis as $key => $value) $j .= "<a href='?activity&jenis=$value data-aos='fade-up'' class='proper btn btn-info mb2'>$value</a> ";
  echo "<section><div class=container><div data-aos='fade-up'><p>Silahkan pilih jenis aktivitas:</p>$j</div></div></section>";
  exit;
}

$ryaitu = [
  'latihan' => 'Yaitu praktikum yang persis dicontohkan oleh instruktur atau materi yang sudah disampaikan. Kamu wajib mengerjakannya.',
  'tugas' => 'Yaitu praktikum membangun Aplikasi Web berdasarkan studi kasus dari Dunia Usaha dan Industri (DUDI). Kamu wajib membangunnya menggunakan HTML, CSS, JS, dan PHP.',
  'challenge' => 'Yaitu pembuktian bahwa kamu sudah siap terjun ke Dunia Usaha dan Industri (DUDI). Kamu wajib membangun salah satu portfolio system yang berhasil kamu buat.'
];
$yaitu = $ryaitu[$jenis];

echo "<span class=debug id=jenis>$jenis</span>";

$img_check_path = 'assets/img/icons/check.png';
$pesan_upload = '';
if(isset($_POST['btn_hapus'])){
  $s = "DELETE FROM tb_bukti_$jenis WHERE id=$_POST[id_bukti]";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo "<script>location.replace('?activity&jenis=$jenis&no=$_POST[no_jenis]')</script>";
  exit;
}

if(isset($_POST['btn_upload'])){

  $id_jenis = $_POST['id_jenis'];
  $s = "SELECT a.*,b.*  
  FROM tb_assign_$jenis a 
  JOIN tb_act_$jenis b ON a.id_$jenis=b.id 
  WHERE a.id=$id_jenis";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);

  $no_jenis = $d['no'];
  $tanggal_jenis = $d['tanggal'];
  $basic_point = $d['basic_point'];
  $bonus_point = $d['bonus_point'];
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

  $rtarget = [
    'latihan' => "uploads/$folder_uploads/$jenis$no_jenis.jpg",
    'tugas' => "uploads/$folder_uploads/$jenis$no_jenis.zip",
    'challenge' => '',
  ];
  
  $target = $rtarget[$jenis];

  // echo "
  // ($selisih-$ontime_dalam*60)/$ontime_deadline*60*$ontime_point; <br>
  // ontime_point:$ontime_point<br>
  // selisih:$selisih<br>
  // telat_point:$telat_point<br>
  // sisa_ontime_point:$sisa_ontime_point<br>
  // get_point:$get_point<br>
  // ";

  $s = "SELECT id as id_bukti FROM tb_bukti_$jenis WHERE id_$jenis=$id_jenis and id_peserta=$id_peserta";
  
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    $kolom_link = $jenis=='challenge' ? ',link' : '';
    $link_value = $jenis=='challenge' ? ",'$_POST[bukti]'" : '';
    $s = "INSERT INTO tb_bukti_$jenis (id_$jenis,id_peserta,get_point$kolom_link) VALUES ($id_jenis,$id_peserta,$get_point$link_value)";
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
    $pesan_upload = div_alert('info','Kamu sudah upload bukti tugas sebelumnya, replace berhasil.');
  }  

  if(isset($_FILES['bukti'])){
    if(move_uploaded_file($_FILES['bukti']['tmp_name'],$target) && $jenis!='challenge'){
      // die("<pre>$s</pre>");
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      echo $pesan_upload;
      echo "<script>location.replace('?activity&jenis=$jenis&no=$no_jenis')</script>";
      exit;
    }else{
      $pesan_upload = div_alert('danger','Tidak dapat move_uploaded_file.');
    }    
  }else{
    // for challenge
    // tanpa upload file
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo $pesan_upload;
    echo "<script>location.replace('?activity&jenis=$jenis&no=$no_jenis')</script>";
    exit;
  }

}

# ============================================
# NORMAL FLOW
# ============================================
if($no==''){
  $s = "SELECT a.no, b.nama,
  (
    SELECT 1 FROM tb_bukti_$jenis 
    WHERE status=1 
    AND id_$jenis=a.id 
    AND id_peserta=$id_peserta) sudah_mengerjakan   
  FROM tb_assign_$jenis a 
  JOIN tb_act_$jenis b ON a.id_$jenis=b.id 
  WHERE no is not null 
  AND kelas='$kelas'
  order by no";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    echo '<section><div class=container>';
    echo div_alert('danger',"Maaf, belum ada data $jenis untuk kelas $kelas.");
    echo '</div></section>';
  }else{
    $rno = '';
    while ($d=mysqli_fetch_assoc($q)) {
      $primary = $d['sudah_mengerjakan'] ? 'success' : 'primary';
      $rno .= "<a class='btn btn-$primary btn-sm mb2' href='?activity&jenis=$jenis&no=$d[no]'>$d[no]. $d[nama]</a> ";
    }
    echo "
    <section>
      <div class=container data-aos='fade-up'>
        Silahkan pilih $jenis:
        <div class=wadah>
          $rno
        </div>
        <div class='kecil miring'>
          <span class=hijau>hijau: sudah dikerjakan</span>; <span class=biru>biru: belum kamu kerjakan</span>
        </div>
      </div>
    </section>";
  }
  
  // exit;

}else{
  include 'activity_show.php';
}
