<style>
  .show_bukti{cursor: pointer; color:darkblue;font-style:italic;transition:.2s}
  .show_bukti:hover{letter-spacing: 1px}
</style>
<?php
$select_link = $jenis!='challenge' ? '0' : "link FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis";
$select_sublevel = $jenis!='challenge' ? '0' : "r.nama FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  JOIN tb_sublevel_challenge r ON p.id_sublevel=r.id
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis";

$s = "SELECT a.id,
a.folder_uploads,
a.nama,
c.kelas,
(
  SELECT p.id FROM tb_bukti_$jenis p
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis) id_jenis,
(
  SELECT 1 FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis) sudah_mengerjakan ,
(
  SELECT 1 FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis 
  AND tanggal_verifikasi is null) belum_verif,
(
  SELECT 1 FROM tb_bukti_$jenis p 
  JOIN tb_assign_$jenis q ON p.id_assign_$jenis=q.id 
  WHERE p.id_peserta=a.id 
  AND q.id_$jenis=$id_jenis 
  AND tanggal_verifikasi is not null 
  AND status=-1) kena_reject,
(
  SELECT $select_link) link,
(
  SELECT $select_sublevel) sublevel 

FROM tb_peserta  a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE a.id_role=1  
AND a.status = 1 
AND c.status = 1 
AND d.id_room=$id_room 
AND a.nama NOT LIKE '%DUMMY'
ORDER BY c.kelas,a.nama
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$rsudah = [];
$rsudah_kelas = [];
$rsudah_link = [];
$rbelum = [];
$rbelum_kelas = [];
$runverif = [];
$runverif_kelas = [];
$rreject = [];
$rreject_kelas = [];
$rid = [];
$rid_jenis = [];
$rfu_sudah = [];
$rfu_reject = [];
$rfu_unverif = [];
$rlink = [];
$rsublevel = [];
while ($d=mysqli_fetch_assoc($q)) {
  $folder_uploads = $d['folder_uploads'];
  $nama = ucwords(strtolower($d['nama']));
  if($d['sudah_mengerjakan']){
    if($d['belum_verif']){
      array_push($rid,$d['id']);
      array_push($rid_jenis,$d['id_jenis']);
      array_push($runverif,$nama);
      array_push($runverif_kelas,$d['kelas']);
      array_push($rfu_unverif,$folder_uploads);
      if($jenis=='challenge'){
        $rlink[$d['id']] = $d['link'];
        $rsublevel[$d['id']] = $d['sublevel'];
      }
    }else{ // sudah mengerjakan dan sudah verif
      if($d['kena_reject']){
        array_push($rreject,$nama);
        array_push($rreject_kelas,$d['kelas']);
        array_push($rfu_reject,$folder_uploads);
      }else{ // sudah mengerjakan dan sudah verif dan no-reject
        array_push($rsudah,$nama);
        array_push($rsudah_kelas,$d['kelas']);
        array_push($rsudah_link,$d['link']);
        array_push($rfu_sudah,$folder_uploads);
      }
    } 
  }else{
    array_push($rbelum,$nama);
    array_push($rbelum_kelas,$d['kelas']);
  }
}


$sudah = ''; 
foreach ($rsudah as $key => $nama){
  $path_bukti = "uploads/$rfu_sudah[$key]/$jenis-$id_assign.jpg";
  if($jenis=='latihan'){
    if(file_exists($path_bukti)){
      $show_bukti =  "
        <span class=show_bukti id=show_bukti__$id_bukti>Show Accepted Bukti</span>
        <div class='div_bukti hideit' id=div_bukti__$id_bukti>$path_bukti</div>
      ";
    
    }else{
      $show_bukti = "<span class=red>$path_bukti :: Bukti gambar tidak ada.</span>";
    }
  }else{
    $show_bukti = "<a target=_blank href='$rsudah_link[$key]'>Link Bukti</a>";
  }
  
  $sudah .= '<div>'.($key+1).'. '.$rsudah_kelas[$key]." ~ $nama ~ $show_bukti</div> ";
} 

$reject = ''; 
foreach ($rreject as $key => $value){
  $path_bukti = "uploads/$rfu_reject[$key]/$jenis-$id_assign.jpg";
  if(file_exists($path_bukti)){
    $show_bukti =  "
      <span class=show_bukti id=show_bukti__$id_bukti>Show Rejected Bukti</span>
      <div class='div_bukti hideit' id=div_bukti__$id_bukti>$path_bukti</div>
    ";
  
  }else{
    $show_bukti = "<span class=red>$path_bukti :: Bukti gambar tidak ada.</span>";
  }

  $reject .= '<div>'.($key+1).'. '.$rreject_kelas[$key]." ~ $value ~ $show_bukti</div> ";
} 
$belum = ''; foreach ($rbelum as $key => $value) $belum .= '<div>'.($key+1).'. '.$rbelum_kelas[$key]." ~ $value </div> ";


$unverif = ''; 
foreach ($runverif as $key => $value){
  $id_bukti = $rid_jenis[$key];
  if($jenis=='latihan'){
    $path_bukti = "uploads/$rfu_unverif[$key]/$jenis-$id_assign.jpg";
    if(file_exists($path_bukti)){
      $bukti_show =  "
        <span class=show_bukti id=show_bukti__$id_bukti>Show Bukti</span>
        <div class='div_bukti hideit' id=div_bukti__$id_bukti>$path_bukti</div>
      ";

    }else{
      $bukti_show = "<span class=red>$path_bukti :: Bukti gambar tidak ada.</span>";
    }
  }elseif($jenis=='challenge'){
    $link = $rlink[$rid[$key]];
    $sublevel = $rsublevel[$rid[$key]];
    $bukti_show = "
      <div>Sublevel : <span class='f20 darkblue'>$sublevel</span></div>
      Challenge's Link : <a href='$link' target=_blank>$link</a>
    ";
  }

  $btn_accept = $id_role!=3 ? "<button class='btn btn-success btn-sm btn_aksi_old btn-block mb1' id=accept__$id_bukti>Accept</button>" : "<button class='btn btn-success btn-sm btn-block mb1' onclick='alert(\"Anda Login sebagai Supervisor! Terimakasih sudah mencoba Accept $jenis dari Peserta. Poin peserta akan direkap jika sudah terverifikasi oleh instruktur.\")'>Accept</button>";
  
  $btn_reject = $id_role!=3 ? "<button class='btn btn-danger btn-sm btn_aksi_old btn-block mb1' id=reject__$id_bukti>Reject</button>" : "<button class='btn btn-danger btn-sm btn-block mb1' onclick='alert(\"Anda Login sebagai Supervisor! Terimakasih sudah mencoba Reject $jenis dari Peserta. Reject wajib disertai dengan alasan reject agar peserta segera re-upload revisi $jenis-nya.\")'>Reject</button>";
  
  // langsung tampil accept/reject untuk challenge
  $hideit = $jenis=='challenge' ? '' : 'hideit';

  $img_or_zip = "
    <div class=wadah id=blok_bukti__$id_bukti>
      $bukti_show 

      <div class='$hideit' id=btn_accept_reject__$id_bukti> 
        <div class='row mt-2'> 
          <div class='col-sm-6'>
            $btn_accept
          </div>
          <div class=col-sm-6>
            $btn_reject
          </div>
        </div>
      </div>
    </div>
  ";
  $unverif .= ($key+1).". $runverif_kelas[$key] ~ $value; <span class=debug>id_bukti: $id_bukti</span> $img_or_zip<br>";
} 

$belum_diverif = count($runverif)==0 ? '<span class="green miring">-- all verified --</span> ' : '<div class="tebal darkred">Belum diverifikasi '.count($runverif).' peserta:</div>';

$info_reject = count($rreject) ? "<div class='wadah red'>Rejected: $reject</div>" : '';

echo "
  <div class='wadah gradasi-kuning'>
    <h5 class='darkblue proper'>
      Submitter 
      <span class=btn_aksi id=submiter__toggle>$img_detail</span>
    </h5>
  </div>
  <div id=submiter class=hideita>
  ";
  echo '<div class=wadah data-zzz-aos=fade-up>'.$belum_diverif.$unverif.'</div>';
  echo '<div class=wadah data-zzz-aos=fade-up><div class="tebal biru">Dikerjakan oleh '.count($rsudah).' peserta:</div>'.$sudah.$info_reject.'</div>';
  echo '<div class=wadah data-zzz-aos=fade-up><div class="tebal red">Belum mengerjakan '.count($rbelum).' peserta:</div>'.$belum.'</div>';

echo '</div>';

?>
<script>
  $(function(){
    $('.show_bukti').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_bukti = rid[1];
      console.log(id_bukti);
      $('#div_bukti__'+id_bukti).html("<img src='"+$('#div_bukti__'+id_bukti).text()+"' class='img-fluid' />");
      $('#div_bukti__'+id_bukti).slideDown();
      $('#btn_accept_reject__'+id_bukti).slideDown();
      $(this).slideUp();
    })
  })
</script>