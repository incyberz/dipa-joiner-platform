<style>
.gradasi-abu{background:linear-gradient(#ddd,#bbb); height:100%;}
.gradasi-abu:hover{background:linear-gradient(#eee,#bdb)}
.btn-active{background:linear-gradient(#cfc,#afa); border:solid 2px blue;}
.btn-active:hover{background:linear-gradient(#dfd,#bdb)}
</style>
<section id="pengajar" class="pengajar section-bg">
  <div class="container">
<!-- ========================================================== -->
<?php
login_only();
$fitur_dosen = $id_role==1 ? '' : "<div class='wadah mt2 gradasi-merah'>Fitur Dosen: <a href='?polling_hasil'>Hasil Polling</a></div>";

echo "
<div class='section-title' data-aos='fade'>
  <h2>Polling</h2>
  <p>Assalamu'alaikum! Halo semuanya! Agar DIPA Joiner semakin baik, kami membutuhkan feedback dari kamu. Silahkan polling dengan sejujur-jujurnya. Terimakasih!</p>
  $fitur_dosen
</div>
";
$get_dari = $_GET['dari'] ?? '?';
$dari = urldecode($get_dari);


if(isset($_POST['btn_submit'])){
  $s = "INSERT INTO tb_polling_answer (id, jawabans,saran,nama_responden) 
  VALUES ($id_peserta,'$_POST[jawabans]','$_POST[saran]','$_POST[nama_responden]') 
  ON DUPLICATE KEY UPDATE 
  jawabans='$_POST[jawabans]', 
  saran='$_POST[saran]',
  nama_responden='$_POST[nama_responden]',
  tanggal=current_timestamp
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  echo div_alert('success', "Terima kasih <span class='tebal darkblue'>$_POST[nama_responden]</span> atas Polling dan Saran Anda. | <a href='$dari'>Back</a>");
  exit;
}



# =========================================================
# CEK JIKA SUDAH POLLING
# =========================================================
$s = "SELECT * FROM tb_polling_answer WHERE id=$id_peserta";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_jawaban = [];
$nama_responden = 'RESPONDEN-'.rand(111,999);
if(mysqli_num_rows($q)){
  $d=mysqli_fetch_assoc($q);
  $jawabans = $d['jawabans'];
  $saran = $d['saran'];
  $nama_responden = $d['nama_responden'] ?? $nama_responden;
  $tanggal_polling = date('d M Y H:i:s',strtotime($d['tanggal']));

  $rj = explode('|',$jawabans);
  foreach ($rj as $j) {
    if(strlen($j)>2){
      $rjj = explode('-',$j);
      $arr_jawaban[$rjj[0]] = $rjj[1]; 
    }
  }
  // echo "<pre>";
  // var_dump($rj);
  // echo "</pre>";
  // echo "<pre>";
  // var_dump($arr_jawaban);
  // echo "</pre>";
  $gradasi_merah = '';
  $hideit_saran = '';
  $submit_caption = 'Re-Submit';
  $kamu_sudah_polling = "<div class='mt2 kecil biru'>Kamu sudah pernah polling pada tanggal $tanggal_polling</div>";
}else{
  $kamu_sudah_polling = '';
  $submit_caption = 'Submit';
  // $submit_disabled = 'disabled';
  $hideit_saran = 'hideit';
  $gradasi_merah = 'gradasi-merah';
  $jawabans = '';
  $saran = '';
} 




# =========================================================
# GET POLLING DATA
# =========================================================
$s = "SELECT * FROM tb_polling WHERE untuk='uts' ORDER BY no,id";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$rpolling = [];
while ($d=mysqli_fetch_assoc($q)) {
  $rpolling[$d['no']] = [$d['pertanyaan'], $d['respon']];
}

// $rpolling[2] = ['Apakah kamu merasa termotivasi dengan adanya Fitur Dynamic Leaderboard dan My-Rank?','termotivasi'];
// $rpolling[3] = ['Ujian online membutuhkan internet yang lancar dan tentunya kamu harus membeli kuota internet. Apakah kamu keberatan jika Ujian offline di kertas digantikan dengan Ujian Offline tapi menjawabnya secara online via HP kamu?','keberatan'];
// $rpolling[4] = ['Setiap latihan atau challenge memberikan poin, pembelajaran, dan kesempatan untuk menempati Rank tertinggi. Apakah kamu merasa tertekan dengan banyaknya latihan dan challenge pada DIPA Joiner?','tertekan'];
// $rpolling[5] = ['Setujukah kamu jika MK lain pun memakai DIPA Joiner?','setuju'];
// $rpolling[6] = ['DIPA Joiner diakses oleh ratusan Mhs saat UTS, apakah aplikasi ini tetap cepat dalam hal merespon request klik dari HP kamu? berilah rating untuk Speed Access aplikasi ini!','rate'];
// $rpolling[7] = ['Secara umum apakah Aplikasi ini sudah bagus?','rate'];

$count_rpolling = count($rpolling);
$polls = "<span class=debug><span id=jumlah_jawabans>$count_rpolling</span></span>";
foreach ($rpolling as $no => $rtanya) {
  $polls.="<span class=debug><span class=jawabans id=jawabans__$no></span></span>";
  if($rtanya[1]=='rate'){
    //rate
    $opsi = '';
    for ($i=1; $i <=5 ; $i++) { 
      $no_counter = $no."__$i";
      $opsi .= "
      <img id=stars__$no_counter class='zoom pointer stars stars__$no aksi' src=assets/img/icons/stars.png height=40px> 
      ";
    }
    $opsi = "<div class='tengah mt2 mb4'>$opsi</div>";
  }else{
    // setuju / tdk setuju
    $opsi = "
      <div class='row mt2 mb4'>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi btn__$no' id=btn__$no"."__1>Tidak $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi btn__$no' id=btn__$no"."__2>Sedikit $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi btn__$no' id=btn__$no"."__3>Saya $rtanya[1]</span></div>
        <div class='col-md-3 mb2'><span class='btn gradasi-abu btn-block aksi btn__$no' id=btn__$no"."__4>Sangat $rtanya[1]</span></div>
      </div>
    ";

  }

  // show pertanyaan
  $polls.= "
    <div class='btop pt2 mb2 $gradasi_merah p1 ' id=polls__$no>
      $no
      <div class=mb2>$rtanya[0]</div>
      $opsi
    </div>
  ";
}

?>

<div class="wadahzzz">
  <h4 class='darkblue mb2 mt2'>Polling:</h4>
  <div class="form-group mt2 mb3">
    <label for="nama">Nama</label>
    <input type="text" class=form-control disabled value='<?=$nama_responden?>'>
    <small class='ml-1 miring abu'>Identitas kamu akan kami rahasiakan.<?=$kamu_sudah_polling?></small>
  </div>
  <?=$polls?>
  <form method=post>

    <input type="hidden" name=nama_responden value='<?=$nama_responden?>'>
    <input type="hidden" id=jawabans name=jawabans>
    <div class="form-group mb2 mt2 <?=$hideit_saran?>" id=blok_saran>
      <h4 class="darkblue"><label for="saran">Saran dan masukan:</label></h4>
      <textarea required class="form-control" name="saran" id="saran" rows="10"><?=$saran?></textarea>
      <small><i>Masukan saran dan masukan dari kamu agar aplikasi ini semakin baik, atau saran untuk instruktur, bagaimana cara mengajarnya atau tentang materi yang disampaikan --- <span class='abu miring'>jika tidak ada silahkan strip</span></i></small>
    </div>
    <button class="btn btn-primary btn-block" id=btn_submit name=btn_submit disabled><?=$submit_caption?></button>
  </form>
</div>


<!-- ========================================================== -->
  </div>
</section>
<script>
  $(function(){
    let jawaban_set = new Set();
    let jumlah_jawabans = parseInt($('#jumlah_jawabans').text());

    $('.aksi').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let no = rid[1];
      let counter = rid[2];

      if(aksi=='btn'){
        $('.btn__'+no).removeClass('btn-active');
        $(this).addClass('btn-active');
      }else if(aksi=='stars'){
        $('.stars__'+no).prop('src','assets/img/icons/stars.png');
        for (let i = 1; i <= counter; i++) {
          $('#stars__'+no+'__'+i).prop('src','assets/img/icons/stars_red.png');
        }
      }

      $('#jawabans__'+no).text(no+'-'+counter);
      
      let z = document.getElementsByClassName('jawabans');
      let jawabans='';
      for (let i = 0; i < z.length; i++) {
        jawabans += z[i].innerText + '|';
      }
      $('#jawabans').val(jawabans);
      
      jawaban_set.add(no);
      if(jawaban_set.size==jumlah_jawabans){
        $('#blok_saran').slideDown();
        $('#btn_submit').prop('disabled',false);
      }

      $('#polls__'+no).removeClass('gradasi-merah');

    })
  })
</script>