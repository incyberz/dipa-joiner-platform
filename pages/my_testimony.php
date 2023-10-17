<?php
$judul = 'My Testimony';
$jumlah_karakter = strlen($testimony);

$pesan = '';
if(isset($_POST['btn_simpan_testimony'])){
  $pekerjaan = clean_sql($_POST['pekerjaan']);
  $testimony = clean_sql($_POST['testimony']);

  $s = "UPDATE tb_peserta SET pekerjaan='$pekerjaan',testimony='$testimony' where id=$id_peserta";
  $q = mysqli_query($cn,$s) or die(mysqli_error($q));
  $pesan = div_alert('success','Update testimony sukses. | <a href="?peserta">Cek di Menu Peserta</a>');
}

















?>
<section id="upload_profil" class="upload_profil">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2><?=$judul?></h2>
      <!-- <p>This page is ready to code ...</p> -->
      <?=$pesan?>
    </div>

    <div class="wadah gradasi-hijau" data-aos="fade-up" data-aos-delay=150>
      <form method=post>
        <div class="form-group">
          <div class=mb1>Saya sebagai:</div>
          <input class='form-control' name=pekerjaan value='<?=$pekerjaan?>' minlength=5 maxlength=20 required placeholder='Muridnya Pa Iin'>
          <div class="kecil miring abu ml2 mt1 mb4">)* isilah dengan pekerjaanmu, atau sebagai pelajar, ataupun hobbies minat bakat.</div>
        </div>
        <div class="form-group">
          <div class=mb1>Testimoni kamu tentang adanya JWD-VSGA ini:</div>
          <textarea class='form-control' name="testimony" id=testimony minlength=50 maxlength=500 required placeholder='Sangat bermanfaat, bisa dapet sertifikat gratisan, coba kalo offline, praktikumnya pasti seru!!' ><?=$testimony?></textarea>
          <div class="kecil miring abu ml2 mt1 mb4">)* isi dengan 50 s.d 500 karakter. Kamu mengetik <span id="jumlah_karakter"><?=$jumlah_karakter?></span> karakter.</div>
        </div>
        <div class="form-group">
          <button class='btn btn-primary btn-block' name=btn_simpan_testimony id=btn_simpan_testimony disabled>Simpan Testimony</button>
        </div>
      </form>
    </div>




  </div>
</section>


<script>
  $(function(){
    $('#testimony').keyup(function(){
      let len = $(this).val().trim().length;
      $('#jumlah_karakter').text(len);
      let disabled = len>=50 ? 0 : 1;
      $('#btn_simpan_testimony').prop('disabled',disabled);
    })
  })
</script>