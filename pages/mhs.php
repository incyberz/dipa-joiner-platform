<?php
$data = '';
$s = "SELECT * FROM tb_kelas WHERE id_room=$id_room AND status=1 ORDER BY shift, prodi";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

while($d=mysqli_fetch_assoc($q)){
  // $id=$d['id'];

  $s2 = "SELECT * FROM tb_peserta WHERE kelas='$d[kelas]' ORDER BY nama";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

  $mhs = '';
  while($d2=mysqli_fetch_assoc($q2)){
    $nama = ucwords(strtolower($d2['nama']));

    $mhs.="
    <div class='kecil tengah abu'>
      <img src='assets/img/peserta/wars/peserta-$d2[id].jpg' class='foto_profil'>
      <div>$nama</div>
    </div>";
  }


  $data .= "
    <div class='wadah content' data-aos='fade-up' data-aos-delay='150'>
      $d[kelas]
      <div class='wadah flexy'>
        $mhs
      </div>      
    </div>
  ";
}

?>
<section id="about" class="about">
  <div class="container">

    <div class="section-title" data-aos="fade-up">
      <h2>Peserta Mhs</h2>
      <p>Peserta Mhs MK <?=$room?></p>
    </div>

    <?=$data?>
  </div>
</section>
