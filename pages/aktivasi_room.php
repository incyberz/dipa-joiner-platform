<?php
instruktur_only();

if (isset($_POST['reset_awal_sesi'])) {
  $s = "UPDATE tb_room SET awal_sesi=NULL, status=2 WHERE id=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Reset Awal Sesi sukses.');
  jsurl('', 2000);
  exit;
}
if (isset($_POST['btn_batalkan_aktivasi'])) {
  unset($_SESSION['dipa_id_room']);
  jsurl();
}
if (isset($_POST['btn_aktivasi'])) {
  echolog('Validation room data');

  $new_status = $_POST['btn_aktivasi'];
  unset($_POST['btn_aktivasi']);

  if ($_POST) {
    $pairs = '__';
    foreach ($_POST as $key => $value) {
      if (!$value) {
        echo div_alert('danger', "Input aktivasi tidak boleh dikosongkan.");
        jsurl('', 3000);
        exit;
      } else {
        $value = clean_sql($value);
        $pairs .= ",$key='$value'";
      }
    }
    $pairs = str_replace('__,', '', $pairs);

    $s = "UPDATE tb_room SET $pairs,status=$new_status WHERE id=$id_room";
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Update sukses.');
    jsurl('', 1000);
  } else {
    echo div_alert('danger', 'Tidak ada data yang bisa diproses.');
    jsurl('', 2000);
  }



  exit;
}





















# ============================================================
# ARRAY STATUS ROOM 
# ============================================================
include 'include/arr_status_room.php';

# ============================================================
# SET HEADER
# ============================================================
$status_room = $status_room == '' ? 0 : $status_room;
set_h2('Aktivasi Room', "
  Aktivasi Room bertujuan agar Room siap dipakai oleh peserta.
  <div class='wadah mt1 gradasi-toska f20 darkblue'>
    Status Room : $arr_status_room[$status_room] <span class=consolas>[$status_room]</span>
  </div>
");

# ============================================================
# NEXT STATUS
# ============================================================
$next_status = $status_room + 1;
$inputs = div_alert('danger', "Belum ada komponen input untuk Next Status : $next_status");

# ============================================================
# INPUT BY STATUS NUMBER
# ============================================================
$src = "aktivasi_room-status-$next_status.php";
if (file_exists($src)) {
  include $src;
}

echo "
<div class='tebal abu miring'>Verifikasi Tahap $next_status</div>
<h3>$arr_status_room[$next_status]</h3>
<p>$arr_status_room_desc[$next_status]</p>
<form method='post'>
  <div class='wadah gradasi-hijau'>
    $inputs
    <button class='btn btn-primary w-100' name=btn_aktivasi value=$next_status>Aktivasi Berikutnya</button>
  </div>
  <div class=tengah>
    <button class='btn btn-sm btn-secondary' onclick='return confirm(`Batalkan aktivasi room?`)' name=btn_batalkan_aktivasi>Batalkan Aktivasi</button>
  </div>

</form>

";
?>



























<!-- <h3>Room Options</h3>
<p>Opsi-opsi pilihan untuk room ini</p>
<form method='post' class='wadah gradasi-hijau'>
  <div class='mb1'>Jumlah Sesi</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=number name=jumlah_sesi min=3 max=32 value=$jumlah_sesi>
    </div>
    <div>
      <select name='durasi_mid_test' class='form-control'>
        <option value='1'>UTS selama 1 minggu</option>
        <option value='2' selected>UTS selama 2 minggu</option>
        <option value='3'>UTS selama 3 minggu</option>
        <option value='4'>UTS selama 4 minggu</option>
      </select>
    </div>
    <div>
      <select name='durasi_mid_test' class='form-control'>
        <option value='1'>UAS selama 1 minggu</option>
        <option value='2' selected>UAS selama 2 minggu</option>
        <option value='3'>UAS selama 3 minggu</option>
        <option value='4'>UAS selama 4 minggu</option>
      </select>
    </div>
  </div>
  <div class='mt1  f12 abu'>
    <ul>
      <li>Jumlah sesi default adalah 16 kali pertemuan (tanpa UTS dan UAS)</li>
      <li>Durasi UTS/UAS default selama 2 minggu</li>
      <li>Durasi total default adalah 18 minggu, room-closed pada minggu ke-19</li>
    </ul>
  </div>

  <div class='mb1'>Sesi dilaksanakan setiap</div>
  <select name='jeda_hari' class='form-control'>
    <option value='7'>Setiap Minggu</option>
    <option value='1'>Setiap Hari</option>
    <option value='30'>Setiap Bulan</option>
  </select>
  <div class='mt1 mb2 f12 abu'>Default jeda hari adalah setiap minggu (7 hari). Tanggal sesi berikutnya otomatis diisi dengan +7 hari dari sesi sebelumnya </div>

  <div class='mb1'>Pertemuan Pertama (Jadwal Pembelajaran)</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=date value='$senin_skg' name=jadwal_pembelajaran>
    </div>
    <div>
      <input class='form-control' required type=time value='$pukul' name=pukul>
    </div>
    <div>
      Durasi
    </div>
    <div>
      <input class='form-control' required type=number value='90' step=5 min=30 max=360 name=durasi_belajar>
    </div>
    <div>
      menit
    </div>
  </div>
  <div class='mt1 mb2 f12 abu'>Lihat pada jadwal kuliah/pembelajaran! Default adalah Senin minggu ini Pukul 08:00</div>

  <div class='mb1'>Durasi Presensi Online</div>
  <select name='jeda_hari' class='form-control'>
    <option value='1'>Pada awal minggu (hari Ahad) s.d akhir minggu (Ahad depan)</option>
    <option value='2'>Pada awal Senin s.d akhir minggu (Ahad depan)</option>
    <option value='3'>Pada awal Senin s.d Jumat malam</option>
    <option value='4'>Pada hari sesuai sesi berlangsung (24 jam)</option>
    <option value='5'>Pada saat sesi berlangsung hingga tengah malam</option>
    <option value='6'>Pada saat sesi berlangsung hingga sesi berakhir (sangat ketat)</option>
  </select>
  <div class='mt1 mb2 f12 abu'>Untuk kemudahan default untuk presensi online bagi peserta adalah pada minggu tersebut (hari Ahad awal s.d Ahad depan), untuk presensi yang lebih ketat Anda dapat memilih opsi lainnya</div>
  <button class='btn btn-primary w-100' name=btn_save_setting value=room>Save</button>
</form>


























<?php
$tr = '';
for ($i = 1; $i <= 16; $i++) {
  $harian = $i == 7 ? 'UTS' : 'Harian';
  $harian = $i == 14 ? 'UAS' : $harian;
  $tr .= "
    <tr>
      <td>$i</td>
      <td><input class='form-control ' value='Pertemuan ke-$i' /></td>
      <td>$harian</td>
    </tr>
  ";
}

?>

<h3>Room Sesi / Learning Path</h3>
<p>Nama-nama sesi untuk room ini</p>
<form method='post' class='wadah gradasi-hijau'>
  <table class="table">
    <thead>
      <th width=40px><span class="f12">Sesi</span> ke</th>
      <th>Nama Sesi / Pertemuan / Learning Path</th>
      <th>Jenis</th>
    </thead>
    <?= $tr ?>
  </table>
  <button class='btn btn-primary w-100' name=btn_save_setting value=room>Save</button>
</form>
<h3>Room Kelas</h3>
<p>Grup kelas yang boleh mengakses room ini</p>
<h3>Sistem Presensi</h3>
<p>Penentuan waktu agar sistem notifikasi berjalan lancar</p> -->


<?php


// variabel awal
$nama_room = 'Room Test';
$singkatan_room = 'RTEST';
$tahun_ajar = date('Y');
$prodi = 'MBS';
$fakultas = 'FEBI';
$jumlah_sesi = 16;
$pukul = '08:00:00';




if (isset($_POST['btn_buat_room'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  // exit;
}



$add = (16 + 2) * 7;
// $ahad_p17 = date('Y-m-d', strtotime("+$add day", strtotime($ahad_skg)));

























?>
<!-- <h3>Room Options</h3>
<p>Opsi-opsi pilihan untuk room ini</p>
<form method='post' class='wadah gradasi-hijau'>


  <div class='mb1'>Pertemuan Pertama (Jadwal Pembelajaran)</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=date value='$senin_skg' name=jadwal_pembelajaran>
    </div>
    <div>
      <input class='form-control' required type=time value='$pukul' name=pukul>
    </div>
    <div>
      Durasi
    </div>
    <div>
      <input class='form-control' required type=number value='90' step=5 min=30 max=360 name=durasi_belajar>
    </div>
    <div>
      menit
    </div>
  </div>
  <div class='mt1 mb2 f12 abu'>Lihat pada jadwal kuliah/pembelajaran! Default adalah Senin minggu ini Pukul 08:00</div>

  <div class='mb1'>Durasi Presensi Online</div>
  <select name='jeda_hari' class='form-control'>
    <option value='1'>Pada awal minggu (hari Ahad) s.d akhir minggu (Ahad depan)</option>
    <option value='2'>Pada awal Senin s.d akhir minggu (Ahad depan)</option>
    <option value='3'>Pada awal Senin s.d Jumat malam</option>
    <option value='4'>Pada hari sesuai sesi berlangsung (24 jam)</option>
    <option value='5'>Pada saat sesi berlangsung hingga tengah malam</option>
    <option value='6'>Pada saat sesi berlangsung hingga sesi berakhir (sangat ketat)</option>
  </select>
  <div class='mt1 mb2 f12 abu'>Untuk kemudahan default untuk presensi online bagi peserta adalah pada minggu tersebut (hari Ahad awal s.d Ahad depan), untuk presensi yang lebih ketat Anda dapat memilih opsi lainnya</div>
  <button class='btn btn-primary w-100' name=btn_save_setting value=room>Save</button>
</form>


























<?php
$tr = '';
for ($i = 1; $i <= 16; $i++) {
  $harian = $i == 7 ? 'UTS' : 'Harian';
  $harian = $i == 14 ? 'UAS' : $harian;
  $tr .= "
    <tr>
      <td>$i</td>
      <td><input class='form-control ' value='Pertemuan ke-$i' /></td>
      <td>$harian</td>
    </tr>
  ";
}

?>

<h3>Room Sesi / Learning Path</h3>
<p>Nama-nama sesi untuk room ini</p>
<form method='post' class='wadah gradasi-hijau'>
  <table class="table">
    <thead>
      <th width=40px><span class="f12">Sesi</span> ke</th>
      <th>Nama Sesi / Pertemuan / Learning Path</th>
      <th>Jenis</th>
    </thead>
    <?= $tr ?>
  </table>
  <button class='btn btn-primary w-100' name=btn_save_setting value=room>Save</button>
</form>
<h3>Room Kelas</h3>
<p>Grup kelas yang boleh mengakses room ini</p>
<h3>Sistem Presensi</h3>
<p>Penentuan waktu agar sistem notifikasi berjalan lancar</p> -->


<?php


// variabel awal
$nama_room = 'Room Test';
$singkatan_room = 'RTEST';
$tahun_ajar = date('Y');
$prodi = 'MBS';
$fakultas = 'FEBI';
$jumlah_sesi = 16;
$pukul = '08:00:00';




if (isset($_POST['btn_buat_room'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  // exit;
}



$add = (16 + 2) * 7;
// $ahad_p17 = date('Y-m-d', strtotime("+$add day", strtotime($ahad_skg)));



echo "

";
