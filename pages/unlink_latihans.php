<h1>UNLINK LATIHAN</h1>
<?php
include 'includes/resize_img.php';

$s = "SELECT id,nama,folder_uploads FROM tb_peserta ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id = $d['id'];
  $nama = $d['nama'];
  $folder_uploads = $d['folder_uploads'];

  if ($folder_uploads == '') continue;
  $path = "uploads/$folder_uploads";
  if (file_exists($path)) {
    $files = scandir($path);

    $li = '';
    foreach ($files as $f) {
      if ($f == '.' || $f == '..') continue;
      // unlink("$path/$f");
      $src = "$path/$f";
      $pesan = resize_img($src);
      $li .= "<li>resizing... $src... $pesan</li>";
    }
    $li = $li ? "<ul>$li</ul>" : '';

    echo "<hr>$id - $nama - $folder_uploads
      $li
    ";
  }
  if ($i == 2) exit;
}
