<?php
$s = "SELECT id, id_role FROM tb_peserta WHERE username='$username'";
$q = mysqli_query($cn,$s) or die('error at instruktur_only.php : '.mysqli_error($cn));
if(mysqli_num_rows($q)==0) die('error at instruktur_only.php : username data not found.');
$d = mysqli_fetch_assoc($q);
$id_peserta = $d['id'];