<?php
$a = $_SERVER['REQUEST_URI'];
?>
<h1 class="merah tebal">404</h1>
<h3 class="merah tebal">Page Not Found!</h3>
<p class="kecil miring abu">Page yang Anda akses tidak ada, silahkan klik pada Menu yang tersedia.</p>
<hr>
<p>Jangan khawatir, sistem telah mencatatnya... :)</p>
<hr>

Broken-Link: <i><?= $a ?></i> has been saved at <?= date("Y-m-d H:i:s") ?>. Programmer will be soon fixed it!