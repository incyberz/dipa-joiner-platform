<?php 
$pesan = $_GET['pesan'] ?? '';
$pesan = $pesan=='' ? div_alert('info','Tidak ada pesan untuk Anda saat ini. | <a href="?">Home</a>') : $pesan; ?>

<section id="hero" class="d-flex align-items-center">
  <div class="container">
    <?=$pesan?>
  </div>
</section>
