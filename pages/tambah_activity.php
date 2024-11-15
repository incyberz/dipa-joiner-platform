<?php
$p = $_GET['p'] ?? die(erid('p'));
$id_sesi = $_GET['id_sesi'] ?? die(erid('id_sesi'));

$s = "SELECT nama, 
(SELECT zzz) nomor_sesi 
FROM tb_sesi WHERE id=$id_sesi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Invalid sesi.'));
$csesi = mysqli_fetch_assoc($q);

$q = mysqli_query($cn, $select_all_from_tb_room_kelas) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Belum ada Room Kelas.'));

$untuk_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  // $id=$d['id'];
  $k = $d['kelas'];
  $untuk_kelas .= "<div class='kiri'><label><input type=checkbox checked name=untuk_kelas[$k]> $k</label></div>";
}

$not_p = $p == 'latihan' ? 'challenge' : 'latihan';

echo "
  <div class='flexy flex-center tengah'>
    <form method=post style='max-width:600px; min-width:300px'>
      <div class='f12 kiri mb1'><b>Untuk sesi:</b> $csesi[nama]</div>
      <div class='f12 tebal kiri mb1'>Untuk kelas:</div>
      $untuk_kelas
      <input class='form-control mt4 mb2' required minlength=3 maxlength=30 placeholder='Nama $p' name=nama>
      <button class='btn btn-primary proper w-100' name=btn_add>Add $p</button>
      <hr>
      <a href='?tambah_activity&p=$not_p&id_sesi=$id_sesi' class='f10 abu'>Add $not_p</a>
    </form>
  </div>

";
