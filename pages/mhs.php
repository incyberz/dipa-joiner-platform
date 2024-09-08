<?php
$data = '';
$s = "SELECT * FROM tb_kelas WHERE id_room=$id_room AND status=1 ORDER BY shift, prodi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

while ($d = mysqli_fetch_assoc($q)) {
  // $id=$d['id'];

  $s2 = "SELECT * FROM tb_peserta WHERE kelas='$d[kelas]' ORDER BY nama";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

  $peserta = '';
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $nama = ucwords(strtolower($d2['nama']));

    $peserta .= "
    <div class='kecil tengah abu'>
      <img src='$lokasi_profil/$d2[war_image]' class='foto_profil'>
      <div>$nama</div>
    </div>";
  }


  $data .= "
    <div class='wadah content' data-aos='fade-up' data-aos-delay='150'>
      $d[kelas]
      <div class='wadah flexy'>
        $peserta
      </div>      
    </div>
  ";
}

?>

<div class="section-title" data-aos="fade-up">
  <h2>Daftar Peserta</h2>
  <p>Daftar Peserta MK <?= $singkatan_room ?></p>
</div>

<?= $data ?>