<?php
$judul = 'Profile';

?>
<style>
  .blok_upload {
    max-width: 500px;
    margin: auto
  }

  .example {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 50%
  }
</style>

<?php
if (!$id_peserta) jsurl('?'); // jika auto-loogut
$blok_status = '';
$src_profil_perang = "$lokasi_profil/$d_peserta[war_image]";

if (isset($_POST['btn_upload'])) {
  unset($_POST['btn_upload']);
  echo '<div class="f18 consolas">Processing upload war profiles...</div><hr>';
  $date = date('ymdHis');
  $nama = strtolower(str_replace(' ', '_', $nama_peserta));
  $new_war_image = "$id_peserta-war_unverified-$nama-$date.jpg";
  $target = "$lokasi_profil/$new_war_image";
  echo "<br>$target  ";
  $tmpName = $_FILES['profil_perang']['tmp_name'];

  # ============================================================
  # HAPUS FILE LAMA
  # ============================================================
  echo "<hr>nama file baru:<br>$new_war_image";
  $src = "$lokasi_profil/$d_peserta[war_image]";
  if (file_exists($src) and $d_peserta['war_image'] and $d_peserta['war_image'] != 'war_image_rejected.jpg') {
    if (!unlink($src)) {
      die(div_alert('danger', "Tidak bisa menghapus file profile lama."));
    }
  }

  if (move_uploaded_file($tmpName, $target)) {
    echo '<br>move_uploaded_file... success<br>';

    # ============================================================
    # RESET STATUS PROFIL_OK DAN UPDATE IMAGE
    # ============================================================
    $s = "UPDATE tb_peserta SET war_image='$new_war_image' WHERE id=$id_peserta";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo '<br>updating with unverified war image<br>';

    include 'include/resize_img.php';
    resize_img($target, '', 1000, 1000); // resize ke 150 saat verified
    echo div_alert('success mt2', 'Upload Profil sukses.');
    jsurl('', 2000);
  } else { // gagal move_uploaded_file
    echo '<br>gagal move_uploaded_file...';
  }
}

$src_no_profil_perang = meme_src('soldier');

$alert = 'Upload profil yang cocok buat kamu perang!!';

$status_show = '<span class="f12 abu miring consolas">Belum upload.</span>';
$btn_reupload = "<button class='btn btn-secondary btn-sm' id=btn_reupload>Reupload</button>";
$info = '';
$src = "$lokasi_profil/$d_peserta[war_image]";
if ($d_peserta['war_image'] == 'war_image_rejected.jpg') {
  $info = "Kamu sudah upload profil perang akan tetapi instruktur menolaknya, <span class=darkred>mungkin kurang layak</span> untuk Perang! <span class='tebal darkred'>Jangan foto formal!</span> Silahkan <a href='?pengajar'>whatsapp beliau</a> jika ada kesalahan. Sekarang <span class=blue>silahkan reupload sesuai contoh profil</span>.";
  $status_show = "<span class='darkred'>Profil Ditolak, silahkan reupload!</span>";
  $btn_reupload = '';
} else if (strpos($src_profil_perang, 'war_unverified') and file_exists($src_profil_perang)) {
  $info = "Kamu sudah upload profil perang akan tetapi belum diverifikasi oleh instruktur. Silahkan <a href='?pengajar'>whatsapp beliau</a> untuk mempercepat proses verifikasi profil ini. Jika ingin mengubahnya silahkan reupload.";
  $status_show = "<span class='darkred'>Belum diverifikasi</span>";
  $hideit_blok_upload = 'hideit';
} else if (file_exists($src) and $d_peserta['war_image']) {
  $hideit_blok_upload = 'hideit';
  // $src = $src_profil_perang_accepted;
  $info = "Profil perang kamu sudah terverifikasi. Kamu bisa mengakses fitur <a href='?perang_soal'>Perang Soal</a> secara penuh. Jika ingin mengubah kembali foto profil silahkan reupload, namun kamu harus menunggu kembali verifikasi dari instruktur.";
  $status_show = "<span class='green'>Accepted </span>";
} else {
  echo '<pre>';
  var_dump($src);
  echo '</pre>';
  $src = $src_no_profil_perang;
  $hideit_blok_upload = '';
  $btn_reupload = '';
}

$blok_status = "
  <div id='blok_status'>
    <div><span class='abu miring'>Status:</span> $status_show</div>
    <div class='abu kecil mb2'>$info</div>
    $btn_reupload
    <hr>
  </div>
";

$examples = 'examples not available';

?>
<div class="wadah tengah blok_upload" data-aos="fade-up" data-aos-delay="150">
  <h1 class='f12 abu'>Upload Profile Perang</h1>
  <p class='kecil darkblue'>
  <div class='f18 darkblue'>Fotonya muka only ya!!</div> Mesti terlihat wajah! No masker, no sun-glassess! Lihat contoh! Gunakan <a target=_blank href="https://iloveimg.com/crop-image">Tools Cropping</a> jika perlu!</p>
  <div class="text-center">
    <img onclick='alert("<?= $alert ?>")' class='foto_profil' src='<?= $src ?>'>
  </div>
  <?= $blok_status ?>
  <div id="blok_upload" class='<?= $hideit_blok_upload ?>'>
    <form method=post enctype='multipart/form-data'>
      <div class="mb-2 mt-2">
        <input accept='.jpg' class='form-control' type="file" name="profil_perang" required>
      </div>
      <div class="kecil miring abu mt1 mb1 kiri">)* ekstensi harus JPG</div>
      <button class='btn btn-info btn-block btn-sm' name=btn_upload>Upload</button>
    </form>
    <div>
      <div class='kecil darkblue mb2'>Contoh profil yang benar (muka only):</div>
      <?= $examples ?>
    </div>
  </div>
</div>

<div class="tengah kecil mt2" data-aos="fade-up" data-aos-delay="300">Back to <a href="?perang_soal">Perang Soal Home</a></div>



<script>
  $(function() {
    $('#btn_reupload').click(function() {
      $('#blok_upload').slideToggle()
    })
  })
</script>