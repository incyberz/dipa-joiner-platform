<div class="section-title" data-aoszzz="fade-up">
  <?php if ($status_room == -1 and $id_room) echo div_alert('info', meme('closed', 6) . '<hr>Room ini sudah ditutup.');  ?>
  <h2>Join Kelas</h2>
  <p>Sepertinya kamu belum punya kelas pada tahun ajar <?= $tahun_ajar ?>. Silahkan join class atau hubungi Instruktur jika ada kesulitan!</p>
</div>
<?php
echo "<h1>Join Kelas</h1>";

# ====================================================
# PROCESSOR: JOIN KELAS
# ====================================================
if (isset($_POST['btn_join_kelas'])) {
  $kelas = $_POST['btn_join_kelas'];
  $s = "SELECT 1 FROM tb_kelas_peserta a JOIN tb_kelas b ON a.kelas=b.kelas WHERE b.tahun_ajar=$tahun_ajar AND id_peserta=$id_peserta";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    $s = "INSERT INTO tb_kelas_peserta (kelas,id_peserta) VALUES ('$kelas',$id_peserta)";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', "Join Kelas sukses.");
  } else {
    echo div_alert('danger', "Kamu sudah terdaftar pada Grup Kelas.");
  }
  jsurl();
  exit;
}

?>

<div class="wadah">
  <div class="mb2">Available Grup Kelas pada TA <?= $tahun_ajar ?> : </div>
  <form method=post>
    <ol>
      <?php
      $and_inst = $id_role == 2 ? "AND prodi='INST'" : '';

      $s = "SELECT * FROM tb_kelas WHERE tahun_ajar=$tahun_ajar $and_inst ORDER BY fakultas,prodi,shift";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

      while ($d = mysqli_fetch_assoc($q)) {
        $color = $d['shift'] ? 'green' : 'darkblue';
        $btn = $d['fakultas'] == 'ZDEV' ? '<span class="btn btn-secondary btn-sm" onclick="alert(\'Hubungi developer via whatsapp untuk bergabung menjadi DevOps.\')">Developer Only</span>' : "<button class='btn btn-success btn-sm mb2' name=btn_join_kelas value='$d[kelas]' onclick='return confirm(\"Kesempatan hanya 1x. Yakin untuk gabung? \")'>Join</button>";
        echo "<li class='$color'>$d[fakultas] ~ $d[kelas] ~ $btn</li>";
      }
      ?>
    </ol>
  </form>
  <div class="darkred f12"><b>Catatan: </b> Kamu hanya bisa 1x Join Kelas per semester.</div>
</div>