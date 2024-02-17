<?php
$judul = 'Profile';

?>
<style>
.blok_upload{max-width:500px;margin:auto}
.example{width:64px;height:64px;object-fit:cover;border-radius:50%}
</style>

<?php
if(!$id_peserta) jsurl('?'); // jika auto-loogut
$blok_status = '';
$src_profil_perang = "assets/img/peserta/war-$id_peserta.jpg";
$src_profil_perang_accepted = "assets/img/peserta/wars/peserta-$id_peserta.jpg";
$src_profil_perang_rejected = "assets/img/peserta/war-$id_peserta-reject.jpg";
$is_reject = file_exists($src_profil_perang_rejected) ? 1 : 0;

if(isset($_POST['btn_upload'])){
  if(move_uploaded_file($_FILES['profil_perang']['tmp_name'],$src_profil_perang)){
    if(file_exists($src_profil_perang_rejected)) unlink($src_profil_perang_rejected);
    echo div_alert('success',"Upload profil berhasil. Mohon tunggu... redirecting...");
    jsurl('',3000);
  }else{
    echo div_alert('danger',"Upload gagal.");
  }

  echo '<hr><a class="btn btn-primary" href="?upload_profil_perang">Back to Upload Profile Perang</a>';

  exit;
}

$src_no_profil_perang = meme_src('soldier');



$alert = 'Upload profil yang cocok buat kamu perang!!';

$path = 'assets/img/peserta/wars';
$rexample = scandir($path);
$examples = '';
$i=0;
$j=0;
$rand = rand(1,intval(count($rexample)/3));
foreach ($rexample as $x) {
  $j++;
  if($j<$rand) continue;
  if(strpos("salt$x",'-hi.jpg')) continue;

  $i++; 
  if($i>5) break;
  if(strlen($x)>2){
    $examples .= "<img src='$path/$x' class=example />";
  }
}


$status_show = '<span class="f12 abu miring consolas">Belum upload.</span>';
$btn_reupload = '';
$info = '';
if($is_reject){
  $info = "Kamu sudah upload profil perang akan tetapi instruktur menolaknya, <span class=darkred>mungkin kurang layak</span> untuk Perang! <span class='tebal darkred'>Jangan foto formal!</span> Silahkan <a href='?pengajar'>whatsapp beliau</a> jika ada kesalahan. Sekarang <span class=blue>silahkan reupload sesuai contoh profil</span>.";
  $status_show = "<span class='darkred'>Profil Ditolak, silahkan reupload!</span>";
  $src = $src_profil_perang_rejected;

}else if(file_exists($src_profil_perang)){
  $info = "Kamu sudah upload profil perang akan tetapi belum diverifikasi oleh instruktur. Silahkan <a href='?pengajar'>whatsapp beliau</a> untuk mempercepat proses verifikasi profil ini. Jika ingin mengubahnya silahkan reupload.";
  $status_show = "<span class='darkred'>Belum diverifikasi</span>";
  $btn_reupload = "<button class='btn btn-secondary btn-sm' id=btn_reupload>Reupload</button>";

}else if(file_exists($src_profil_perang_accepted)){
  $hideit_blok_upload = 'hideit';
  $src = $src_profil_perang_accepted;
  $info = "Profil perang kamu sudah terverifikasi. Kamu bisa mengakses fitur <a href='?perang_soal'>Perang Soal</a> secara penuh. Jika ingin mengubah kembali foto profil silahkan reupload, namun kamu harus menunggu kembali verifikasi dari instruktur.";
  $status_show = "<span class='green'>Accepted </span>";

}else{
  $src = $src_no_profil_perang;
  $hideit_blok_upload = '';
}    

$blok_status = "
  <div id='blok_status'>
    <div><span class='abu miring'>Status:</span> $status_show</div>
    <div class='abu kecil mb2'>$info</div>
    $btn_reupload
    <hr>
  </div>
";

?>
<div class="wadah tengah blok_upload" data-aos="fade-up" data-aos-delay="150" >
  <h1 class='f12 abu'>Upload Profile Perang</h1>
  <p class='kecil darkblue'><div class='f18 darkblue'>Fotonya muka only ya!!</div> Mesti terlihat wajah! No masker, no sun-glassess! Lihat contoh! Gunakan <a target=_blank href="https://iloveimg.com/crop-image">Tools Cropping</a> jika perlu!</p>
  <div class="text-center">
    <img onclick='alert("<?=$alert?>")' class='foto_profil' src='<?=$src?>'>
  </div>
  <?=$blok_status?>
  <div id="blok_upload" class='<?=$hideit_blok_upload?>'>
    <form method=post enctype='multipart/form-data'>
      <div class="mb-2 mt-2">
        <input accept='.jpg' class='form-control' type="file" name="profil_perang" required>
      </div>
      <div class="kecil miring abu mt1 mb1 kiri">)* ekstensi harus JPG</div>
      <button class='btn btn-info btn-block btn-sm' name=btn_upload>Upload</button>
    </form>
    <div>
      <div class='kecil darkblue mb2'>Contoh profil yang benar (muka only):</div> 
      <?=$examples?>
    </div>
  </div>
</div>

<div class="tengah kecil mt2" data-aos="fade-up" data-aos-delay="300">Back to <a href="?perang_soal">Perang Soal Home</a></div>



<script>
  $(function(){
    $('#btn_reupload').click(function(){$('#blok_upload').slideToggle()})
  })
</script>