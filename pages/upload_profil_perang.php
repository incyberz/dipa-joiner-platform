<?php
$judul = 'Profile';

?>
<style>.blok_upload{max-width:500px}</style>
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

    $no_profil_perang = meme_src('soldier');
    $src_profil_perang = "assets/img/peserta/peserta-$id_peserta-64.jpg";
    $profil_perang = file_exists($src_profil_perang) ? $src_profil_perang : $no_profil_perang;
    $alert = 'Upload profil yang cocok buat kamu perang!!';


    ?>
    <div class="wadah tengah blok_upload" data-aos="fade-up" data-aos-delay="150" >
      <h4>Upload Profile Perang</h4>
      <p class='kecil darkblue'>Mesti gaya, foto close-up!! No masker, no sun-glassess. Lihat contoh!</p>
      <div class="text-center">
        <img onclick='alert("<?=$alert?>")' class='foto_profil' src='<?=$profil_perang?>'>
      </div>
      <form method=post enctype='multipart/form-data'>
        <div class="mb-2 mt-2">
          <input accept='.jpg' class='form-control' type="file" name="profil_perang" required>
        </div>
        <button class='btn btn-info btn-block btn-sm' name=btn_upload>Upload</button>
        <div class="kecil miring abu mt-2">)* ekstensi JPG</div>
      </form>
    </div>

    <div class="tengah kecil" data-aos="fade-up" data-aos-delay="300">Back to <a href="?dashboard">Dashboard</a></div>


  </div>
</section>