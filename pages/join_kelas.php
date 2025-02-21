<?php
if ($status_room == -1 and $id_room) echo div_alert('info', meme('closed', 6) . '<hr>Room ini sudah ditutup.');

set_h2('Join Kelas', "Sepertinya kamu belum punya kelas pada tahun ajar <b class=darkblue>$ta_show</b>. 
Silahkan join class atau hubungi $Trainer jika ada kesulitan!
");

if ($id_role == 2) {
  echo div_alert('success blue', "Auto Create Kelas INSTRUKTUR-$ta_aktif");
  $s = "INSERT INTO tb_kelas (
    
  ) VALUES ()";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}


# ====================================================
# PROCESSOR: JOIN KELAS
# ====================================================
if (isset($_POST['btn_join_kelas'])) {
  $kelas = $_POST['btn_join_kelas'];
  $s = "SELECT 1 FROM tb_kelas_peserta a JOIN tb_kelas b ON a.kelas=b.kelas WHERE b.ta=$ta_aktif AND id_peserta=$id_peserta";
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

$and_inst = $id_role == 2 ? "AND prodi='INST'" : '';

$s = "SELECT * FROM tb_kelas WHERE ta=$ta_aktif $and_inst ORDER BY fakultas,prodi,shift";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$li = '';
if (mysqli_num_rows($q)) {
  while ($d = mysqli_fetch_assoc($q)) {
    $color = $d['shift'] ? 'green' : 'darkblue';
    $btn = $d['fakultas'] == 'ZDEV' ? '<span class="btn btn-secondary btn-sm" onclick="alert(\'Hubungi developer via whatsapp untuk bergabung menjadi DevOps.\')">Developer Only</span>' : "<button class='btn btn-success btn-sm mb2' name=btn_join_kelas value='$d[kelas]' onclick='return confirm(`Kesempatan hanya 1x. Yakin untuk gabung? `)'>Join</button>";
    $li .= "<li class='$color'>$d[fakultas] ~ $d[kelas] ~ $btn</li>";
  }
}

$list = $li ?  "
  <form method=post class=wadah>
    <div class='mb2'>Available Grup Kelas pada TA $ta_show : </div>
    <ol>$li</ol>
    <div class='darkred f12'><b>Catatan: </b> Kamu hanya bisa 1x Join Kelas per semester.</div>
  </form>
" : div_alert('danger tengah', "
  Wahhh... sepertinya Admin belum membuat satupun Grup Kelas pada <b class=darkblue>$ta_show</b>
  <hr>
  Mohon bersabar mungkin website ini sedang maintenance.
  <hr>
  <a href='?logout'>Logout</a>
");

echo "
  $list
";

// if ($id_role == 2 and $username == 'abi') {
//   echo div_alert('success blue', 'Anda Login sebagai INSTRUKTUR dan diperbolehkan untuk membuat kelas baru');
//   $id_room = 0;
//   include 'aktivasi_room-status-6.php';
//   echo $inputs;
// }
