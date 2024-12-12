<?php
# ============================================================
# GET VAR
# ============================================================
$p = $_GET['p'] ?? die(erid('p'));
$id_sesi = $_GET['id_sesi'] ?? die(erid('id_sesi'));
$nama = $_GET['nama'] ?? '';


# ============================================================
# PROCESSORS 
# ============================================================
if (isset($_POST['btn_add'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $nama = trim($_POST['nama']);
  $is_wajib = $_POST['is_wajib'] == 'on' ? 1 : 'NULL';

  // select nama latihan similar
  $s = "SELECT 1 FROM tb_$p WHERE nama like '%$nama%'";
  echolog($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    div_alert('danger', "$p ini sudah ada di database, silahkan pakai nama lainnya!");
  } else { // nama available

    // select max for new_id
    $s = "SELECT (MAX(id) + 1) as new_id FROM tb_$p";
    echolog($s);
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $d = mysqli_fetch_assoc($q);
    $new_id = $d['new_id'];

    // insert lat/chal
    $s = "INSERT INTO tb_$p (
    id,
    id_room,
    nama
    ) VALUES (
    $new_id,
    $id_room,
    '$nama'
    )";
    echolog($s);
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    // loop assign to kelas
    foreach ($_POST['untuk_kelas'] as $id_room_kelas => $is_on) {
      if ($is_on == 'on') {
        $s = "INSERT INTO tb_assign_$p (
          id_sesi,
          id_$p,
          id_room_kelas,
          is_wajib
        ) VALUES (
          $id_sesi,
          $new_id,
          $id_room_kelas,
          $is_wajib
        )";
        echolog($s);
        $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      }
    } // end foreach kelas
    jsurl('?list_sesi', 1000);
  } // end nama available
} // end if 

# ============================================================
# NORMAL FLOW
# ============================================================
$s = "SELECT nama, 
(
  SELECT COUNT(1) FROM tb_sesi 
  WHERE jenis=1 
  AND id_room=$id_room
  AND no <= a.no) pertemuan_ke,
(
  SELECT COUNT(1) FROM tb_assign_$p 
  WHERE id_sesi=a.id 
  ) count_act -- jumlah latihan / challenge 
FROM tb_sesi a WHERE a.id=$id_sesi";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Invalid sesi.'));
$csesi = mysqli_fetch_assoc($q);

$s = "SELECT * FROM tb_room_kelas WHERE id_room=$id_room -- AND ta=$ta";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Belum ada Room Kelas.'));

$untuk_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $id = $d['id'];
  $k = $d['kelas'];
  if ($k == 'INSTRUKTUR') {
    $ada_room_kelas_instruktur = 1;
    $untuk_kelas .= "<div class='kiri hideit'><label><input type=checkbox checked name=untuk_kelas[$id]> $k</label></div>";
    $untuk_kelas .= "<div class='kiri'><label><input type=checkbox checked disabled> $k</label></div>";
  } else {
    if ($d['ta'] != $ta) continue;
    $untuk_kelas .= "<div class='kiri'><label><input type=checkbox checked name=untuk_kelas[$id]> $k</label></div>";
  }
}


$not_p = $p == 'latihan' ? 'challenge' : 'latihan';

if ($csesi['count_act']) {
  $info_wajib = "Sudah ada $csesi[count_act] $p pada sesi ini.";
  $checked_wajib = '';
} else {
  $info_wajib = "Belum ada satupun $p pada sesi ini. <div class=' bold biru'>Anda disarankan mewajibkan $p ini.</div>";
  $checked_wajib = 'checked';
}

echo "
  <div class='flexy flex-center tengah'>
    <form method=post style='max-width:600px; min-width:300px'>
      <div class='f12 kiri mb1'><b>Untuk:</b> <span class='f22 darkblue'>P$csesi[pertemuan_ke] $csesi[nama]</span></div>
      <div class='alert alert-info f12'>$info_wajib</div>
      <div class='f12 tebal kiri mb1'>Kelas:</div>
      $untuk_kelas
      <input class='form-control mt4 mb2' required minlength=3 maxlength=30 placeholder='Nama $p baru...' name=nama value='$nama'>
      <div class=left>
        <label>
          <input type=checkbox name=is_wajib $checked_wajib> 
          Sifat: Wajib dikerjakan.
        </label>
      </div>
      <button class='btn btn-primary proper w-100 mt2' name=btn_add>Add $p</button>
      <div class='f10 abu miring mt2'>Opsi $p akan di-set di tahapan berikutnya.</div>
      <hr>
      <a href='?tambah_activity&p=$not_p&id_sesi=$id_sesi' class='f10 abu'>Add $not_p</a>
    </form>
  </div>

";
