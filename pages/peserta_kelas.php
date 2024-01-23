<?php
$data = '';

// old
$s = "SELECT * FROM tb_kelas WHERE id_room=$id_room AND status=1 ORDER BY shift, prodi";


//new2
$s = "SELECT *, id as id_room_kelas FROM tb_room_kelas a WHERE id_room=$id_room";

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));



while($d=mysqli_fetch_assoc($q)){
  // $id=$d['id'];

  //old
  $s2 = "SELECT * FROM tb_peserta WHERE kelas='$d[kelas]' ORDER BY nama";
  // new
  $s2 = "SELECT * FROM tb_kelas_peserta a 
  JOIN tb_kelas b ON a.kelas=b.kelas 
  JOIN tb_room_kelas c ON b.kelas=c.kelas 
  JOIN tb_peserta d ON a.id_peserta=d.id  
  WHERE c.id=$d[id_room_kelas] AND d.status=1 ORDER BY b.shift, b.prodi";

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
      $d[kelas] | <a href='?assign_peserta&id_room_kelas=$d[id_room_kelas]'>Assign Peserta</a>
      <div class='wadah flexy'>
        $mhs
      </div>      
    </div>
  ";
}

?>

<div class="section-title" data-aos="fade-up">
  <h2>Peserta Kelas</h2>
  <p>Peserta Kelas MK <?=$room?></p>
</div>

<?=$data?>
