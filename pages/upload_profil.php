<?php
$judul = 'Profile';

?>
<section id="upload_profil" class="upload_profil">
  <div class="container">

    <?php
    if(isset($_POST['btn_upload'])){
      $tipe = 'publik';

      $target = "assets/img/peserta/peserta-$id_peserta.jpg";
      // echo $target;

      if(move_uploaded_file($_FILES[$tipe.'_profil']['tmp_name'],$target)){
        $s = "UPDATE tb_peserta SET profil_ok=null WHERE id=$id_peserta";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

        echo '<section><div>'.div_alert('success',"Upload profil berhasil.").'</div></section><script>location.replace("?")</script>';
      }else{
        echo div_alert('danger',"Upload gagal.");
      }

      echo '<hr><a class="btn btn-primary" href="?upload_profil">Back to My Profile</a>';

      exit;
    }

    $no_profil = 'assets/img/no_profil.jpg';
    $src_publik_profil = "assets/img/peserta/peserta-$id_peserta.jpg";

    $src_publik_profil = file_exists($src_publik_profil) ? $src_publik_profil : $no_profil;

    $alert_no_profile = 'Masak sihh kamu ga punya foto,, gak percaya!!';
    $onclick_publik = $src_publik_profil==$no_profil ? $alert_no_profile : 'Foto ini akan terlihat ke semua orang.';





    ?>
    <div class="row">
      <div class="col-lg-4 offset-lg-4">
        <div class="wadah" data-aos="fade-up" data-aos-delay="150">
          <h4>Upload Profile</h4>
          <p>Pilihlah foto profil yang sopan dan terlihat wajah (tanpa masker, kacamata hitam, dll).</p>
          <div class="text-center">
            <img onclick='alert("<?=$onclick_publik?>")' class='foto_profil' src='<?=$src_publik_profil?>'>
          </div>
          <form method=post enctype='multipart/form-data'>
            <div class="mb-2 mt-2">
              <input accept='.jpg' class='form-control' type="file" name="publik_profil" required>
            </div>
            <button class='btn btn-info btn-block' name=btn_upload>Upload</button>
            <div class="kecil miring abu mt-2">)* ekstensi JPG</div>
          </form>
        </div>
      </div>
    </div>

    <div class="tengah kecil" data-aos="fade-up" data-aos-delay="300">Back to <a href="?dashboard">Dashboard</a></div>


  </div>
</section>