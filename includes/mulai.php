<?php
function mulai($parameter, $login_only = true, $lokasi_pages = 'pages')
{
  if ($login_only) login_only();
  $pages = ['styles', 'functions'];
  foreach ($pages as $page) {
    $file = "$lokasi_pages/$parameter-$page.php";
    if (file_exists($file)) include $file;
  }
  return true;
}
