<?php
$s = "SELECT * FROM tb_peserta ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
while ($d = mysqli_fetch_array($q)) {
  $src = "$lokasi_profil/wars/peserta-$d[id].jpg";

  if (file_exists($src) and strlen($d['war_image']) < 17) {
    $i++;
    echo "<hr>#$i updating $id_peserta $d[nama]<br>";

    // new war_image
    $nama = $d['nama'];
    $date = date('ymdHis');
    $nama2 = strtolower(str_replace(' ', '_', trim($nama)));
    $new_war_image = "$d[id]-war-$nama2-$date.jpg";

    // copy file
    echolog("copy: $src");
    copy($src, "$lokasi_profil/$new_war_image");
    echolog("to: $new_war_image");

    // update data _peserta
    $s2 = "UPDATE tb_peserta SET war_image='$new_war_image' WHERE id=$d[id]";
    echolog($s2);
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

    if ($i == 100) exit;
  } elseif (file_exists($src)) {
    echolog("old war image exists | war_image $d[war_image]");
  }
}
