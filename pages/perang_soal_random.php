<?php
// echo div_alert('info tengah', "Mode Random Selected.");
$start = $_GET['start'] ?? '';
if(!$start){
  echo "
  <div class='tebal tengah'>Rules!!</div>
  <ul class=darkred>
    <li>Akan diload 1 s.d 10 soal random milik kawanmu, dan soal yang sudah muncul <span class=red>tidak bisa diload ulang</span></li>
    <li>Soal terjawab baik benar ataupun salah menghasilkan poin dan passive-poin bagi pembuat soal</li>
    <li>Soal ter-reject menghasilkan reject-poin dan poin negatif bagi pembuat soal</li>
    <li>Soal terverifikasi tidak bisa di-reject</li>
  </ul>
  <a class='btn btn-primary btn-block' href='?perang_soal&mode=random&start=1'>Play Quiz!</a>
  ";
}else{
  include 'perang_soal_random_started.php';
}