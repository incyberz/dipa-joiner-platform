<?php
$judul = 'Profile';
if(!$id_peserta) jsurl('?');


if(isset($_POST['btn_upload'])){
  unset($_POST['btn_upload']);
  echo '<div class="f18 consolas">Processing upload images...</div><hr>';
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  // echo '<pre>';
  // var_dump($_FILES);
  // echo '</pre>';

  $target = "assets/img/peserta/peserta-$id_peserta.jpg";
  $tmpName = $_FILES['profil']['tmp_name'];

  if(move_uploaded_file($tmpName, $target)){
    echo '<br>move_uploaded_file... success<br>';
    
    $s = "UPDATE tb_peserta SET profil_ok=NULL WHERE id=$id_peserta";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    echo '<br>reseting profil status... status reseted to null<br>';

    $orig_image = imagecreatefromjpeg($target);
    $image_info = getimagesize($target); 
    $width_orig  = $image_info[0]; // current width as found in image file
    $height_orig = $image_info[1]; // current height as found in image file
  
    if($width_orig>1000 || $height_orig>1000){
      if($width_orig>$height_orig){
        $width = 1000;
        $height = round($height_orig*1000/$width_orig,0);
      }else{
        $height = 1000;
        $width = round($width_orig*1000/$height_orig,0);
      }
      echo "<br>Current resolution: $width_orig x $height_orig px";
      echo "<br>Resize to : $width x $height px";
  
      $destination_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($destination_image, $orig_image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
      // This will just copy the new image over the original at the same filePath.
      imagejpeg($destination_image, $target, 50);  
    }else{
      echo "<br>resolution ($width_orig x $height_orig pixel) accepted... no need to be resized.";
    }

    echo div_alert('success mt2','Upload Profil sukses.');
    jsurl('',2000);
    exit;

  }else{ // gagal move_uploaded_file
    echo '<br>gagal move_uploaded_file...';
  }
}

$no_profil = 'assets/img/no_profil.jpg';
$src_profil = "assets/img/peserta/peserta-$id_peserta.jpg";

$sudah_upload = file_exists($src_profil) ? 1 : 0;
$src_profil = file_exists($src_profil) ? $src_profil : $no_profil;

$alert_no_profile = 'Masak sihh kamu ga punya foto,, gak percaya!!';
$onclick = $src_profil==$no_profil ? $alert_no_profile : 'Foto ini akan terlihat ke semua orang.';

if($sudah_upload){
  $btn_reupload = '<span class="btn btn-secondary btn-sm btn_aksi" id=blok_upload__toggle>Reupload</span>';
  $blok_upload_hide = 'hideit';
  
  if($profil_ok==1){
    $status = '<span class="green">Accepted</span>';
    $status_ket = 'Selamat! Profil kamu sudah diterima oleh instruktur.';
  }elseif($profil_ok==-1){
    $status = '<span class="red">Rejected</span>';
    $status_ket = 'Wah! Tolong pakai profil lain ya!';
  }else{
    $status = '<span class="darkred">Belum diverifikasi</span>';
    $status_ket = 'Dikarenakan banyaknya peserta, mohon bersabar untuk menunggu verifikasi dari instruktur! Silahkan hubungi beliau saat jam kantor!';
  }
}else{
  $status = '<span class="darkred">Belum upload</span>';
  $btn_reupload = '';
  $status_ket = '';
  $blok_upload_hide = '';
}

?>
<div>
  <div style='max-width:500px;margin:auto'>
    <div class="wadah" data-aos="fade-up" data-aos-delay="150">
      <h4>Upload Pas Foto</h4>
      <p>Pilihlah foto profil yang sopan dan terlihat wajah (tanpa masker, kacamata hitam, dll).</p>
      <div class="text-center">
        <img onclick='alert("<?=$onclick?>")' class='foto_profil' src='<?=$src_profil?>'>
      </div>
      <div class="tengah">Status: <?=$status?></div>
      <div class="tengah f12 abu mb2"><?=$status_ket?></div>
      <div class="tengah"><?=$btn_reupload?></div>
      <div id="blok_upload" class="<?=$blok_upload_hide?>">
        <form method=post enctype='multipart/form-data'>
          <div class="mb-2 mt-2">
            <input accept='.jpg' class='form-control' type="file" name="profil" required>
          </div>
          <button class='btn btn-info btn-block' name=btn_upload>Upload</button>
          <div class="kecil miring abu mt-2">)* ekstensi JPG</div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="tengah kecil" data-aos="fade-up" data-aos-delay="300">Back to <a href="?dashboard">Dashboard</a></div>