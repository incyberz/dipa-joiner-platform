<div class='section-title' data-aos-zzz='fade-up'>
  <h2>Buat Room</h2>
  <p>
    Welcome <u><?= $nama_peserta ?></u>! Silahkan isi form berikut untuk Pembuatan Room Baru!
  </p>
</div>
<?php
instruktur_only();
include 'include/date_managements.php';


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
$ahad_p17 = date('Y-m-d', strtotime("+$add day", strtotime($ahad_skg)));



echo "
<form method='post' class='wadah gradasi-hijau'>
  <div class='sub_form'>Form Buat Room Baru</div>
  <div>Nama Room</div>
  <input class='form-control mt1' required minlength='7' maxlength='30' name=nama_room value='$nama_room'>
  <div class='mt1 mb2 f12 abu'>Contoh: Pemrograman Web II (Laravel). 10 s.d 30 karakter</div>

  <div>Singkatan Room</div>
  <input class='form-control mt1' required minlength='3' maxlength='10' name=singkatan_room value='$singkatan_room' >
  <div class='mt1 mb2 f12 abu'>Contoh: PWeb2, tanpa spasi, 3 s.d 10 karakter</div>

  <div class='mb1'>Tahun Ajar</div>
  <div class='flexy'>
    <div>
      <input class='form-control' required type=number name=tahun_ajar min=2023 max=2028 value=$tahun_ajar>
    </div>
    <div>
      <select name='gg' class='form-control'>
        <option value='1'>Ganjil</option>
        <option value='2'>Genap</option>
      </select>
    </div>
    <div>
      <input class='form-control' required name=prodi minlength='3' maxlength='30' placeholder='Prodi...' value='$prodi'>
    </div>
    <div>
      <input class='form-control' required name=prodi minlength='3' maxlength='30' placeholder='Fakultas...' value='$fakultas'>
    </div>

  </div>
  <div class='mt1 mb2 f12 abu'>Contoh: Tahun ajar 2023 Genap, Prodi MBS, Fakultas FEBI</div>

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
      <li>Jumlah sesi default adalah 16 kali pertemuan (termasuk UTS dan UAS)</li>
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


  <div>Awal Aktif (Opening)</div>
  <input class='form-control mt1' required type=date name=awal_aktif value='$ahad_skg'>
  <div class='mt1 mb2 f12 abu'>Awal aktif room ini dapat diakses oleh peserta, default adalah pada awal minggu (hari Ahad)</div>

  <div>Akhir Aktif (Closed)</div>
  <input class='form-control mt1' required type=date name=akhir_aktif  value='$ahad_p17'>
  <div class='mt1 mb2 f12 abu'>Default masa aktif room adalah setelah semua sesi berakhir. Saat room tidak aktif peserta tidak dapat lagi memosting jawaban atau pertanyaan baru. System juga akan melakukan backing-up data agar kestabilan server tetap terjaga. </div>

  <div class='mb2 f12 abu'>Seting diatas dapat Anda ubah kembali pada Menu Manage Room</div>


  <button class='btn btn-primary w-100' name=btn_buat_room>Buat Room </button>
</form>
";
?>