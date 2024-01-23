<?php
$judul = 'Profile';

?>
<style>
.blok_upload{max-width:500px;margin:auto}
.example{height:64px;height:64px;object-fit:cover;border-radius:50%}
</style>

<?php
$blok_status = '';
$src_profil_perang = "assets/img/peserta/war-$id_peserta.jpg";
$src_profil_perang_rejected = "assets/img/peserta/war-$id_peserta-reject.jpg";
$is_reject = file_exists($src_profil_perang_rejected) ? 1 : 0;

if(isset($_POST['btn_upload'])){
  if(move_uploaded_file($_FILES['profil_perang']['tmp_name'],$src_profil_perang)){
    unlink($src_profil_perang_rejected);
    echo '<section><div>'.div_alert('success',"Upload profil berhasil.").'</div></section><script>location.replace("?upload_profil_perang")</script>';
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
foreach ($rexample as $x) {
  $i++; if($i==9) break;
  if(strlen($x)>2){
    $examples .= "<img src='$path/$x' class=example />";
  }
}


echo "is_reject : $is_reject";

$btn_reupload = "<button class='btn btn-secondary btn-sm' id=btn_reupload>Reupload</button>";
if($is_reject){
  $info = "Kamu sudah upload profil perang akan tetapi instruktur menolaknya, <span class=darkred>mungkin kurang layak</span> untuk Perang! <span class='tebal darkred'>Jangan foto formal!</span> Silahkan <a href='?pengajar'>whatsapp beliau</a> jika ada kesalahan. Sekarang <span class=blue>silahkan reupload sesuai contoh profil</span>.";
  $status_show = "<span class='darkred'>Profil Ditolak</span>";
  $src = $src_profil_perang_rejected;
  $btn_reupload = '';

}else if(file_exists($src_profil_perang)){
  $hideit_blok_upload = 'hideit';
  $src = $src_profil_perang;

  if(file_exists("$path/peserta-$id_peserta.jpg")){
    $info = "Profil perang kamu sudah terverifikasi. Kamu bisa mengakses fitur <a href='?perang_soal'>Perang Soal</a> secara penuh. Jika ingin mengubah kembali foto profil silahkan reupload.";
    $status_show = "<span class='darkblue'>Verified</span>";
  }else{
    $info = "Kamu sudah upload profil perang akan tetapi belum diverifikasi oleh instruktur. Silahkan <a href='?pengajar'>whatsapp beliau</a> untuk mempercepat proses verifikasi profil ini. Jika ingin mengubahnya silahkan reupload.";
    $status_show = "<span class='darkred'>Belum diverifikasi</span>";
  }

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
  <h4>Upload Profile Perang</h4>
  <p class='kecil darkblue'>Mesti gaya, foto close-up!! No masker, no sun-glassess. Lihat contoh!</p>
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
      <div class='kecil darkblue mb2'>Contoh profil yang benar:</div> 
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