<?php
# ============================================================
# AKTIVASI JUMLAH SESI
# ============================================================
if ($jumlah_sesi) {

  if (!$room['minggu_normal_uts']) { // minggu_normal_uts belum ada artinya room lama harus diupdate 

    // nomor sesi saat UTS
    $s = "SELECT a.no
    FROM tb_sesi a 
    WHERE a.jenis=2 AND a.id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q)) die('Tidak dapat menemukan nomor [no] sesi UTS. Silahkan tambah dahulu sesi UTS pada Room ini kemudian atur urutan sesi UTS tersebut');
    $d = mysqli_fetch_assoc($q);
    $durasi_uts = mysqli_num_rows($q);
    $no_sesi_uts = $d['no'];

    // select normal sesi dengan nomor < no_sesi_uts
    $s = "SELECT 1 FROM tb_sesi WHERE no<$no_sesi_uts AND jenis=1 AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $minggu_normal_uts = mysqli_num_rows($q);

    // select normal sesi dengan nomor > no_sesi_uts
    $s = "SELECT 1 FROM tb_sesi WHERE no > $no_sesi_uts AND jenis=1 AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $minggu_normal_uas = mysqli_num_rows($q);

    // select minggu_tenang dengan nomor < no_sesi_uts
    $s = "SELECT 1 FROM tb_sesi WHERE no<$no_sesi_uts AND jenis=0 AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $minggu_tenang_uts = mysqli_num_rows($q);

    // select minggu_tenang dengan nomor > no_sesi_uts
    $s = "SELECT 1 FROM tb_sesi WHERE no > $no_sesi_uts AND jenis=0 AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $minggu_tenang_uas = mysqli_num_rows($q);

    // count_sesi_UAS
    $s = "SELECT 1 FROM tb_sesi WHERE jenis=3 AND id_room=$id_room";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $durasi_uas = mysqli_num_rows($q);




    $info_room = "
      <div class=wadah>
        <div class=mb2>Sesi-sesi pada room lama:</div>
        <div class='row mt2'>
          <div class=col-6>
            <div class=wadah>
              <div>Calculated minggu_normal_uts: $minggu_normal_uts</div>
              <div>Calculated minggu_tenang_uts: $minggu_tenang_uts</div>
              <div>Calculated durasi_uts: $durasi_uts</div>
            </div>
          </div>
          <div class=col-6>
            <div class=wadah>
              <div>Calculated minggu_normal_uas: $minggu_normal_uas</div>
              <div>Calculated minggu_tenang_uas: $minggu_tenang_uas</div>
              <div>Calculated durasi_uas: $durasi_uas</div>
            </div>
          </div>
        </div>
        <div class=wadah>Default jeda_sesi: $jeda_sesi</div>
      </div>
      <input type=hidden name=date_created value='$now'>
    ";

    # ============================================================
    # UPDATE ROOM WITH CALCULATED COUNT
    # ============================================================
    $s = "UPDATE tb_room SET 
    minggu_normal_uts = $minggu_normal_uts,
    minggu_normal_uas = $minggu_normal_uas,
    minggu_tenang_uts = $minggu_tenang_uts,
    minggu_tenang_uas = $minggu_tenang_uas,
    durasi_uts = $durasi_uts,
    durasi_uas = $durasi_uas,
    jeda_sesi = $jeda_sesi

    WHERE id=$id_room
    ";
    echolog($s);
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $inputs = $info_room;
  } else {
    $info_room = "
      <div class=wadah>
        <div>minggu_normal_uts: $room[minggu_normal_uts]</div>
        <div>minggu_tenang_uts: $room[minggu_tenang_uts]</div>
        <div>durasi_uts: $room[durasi_uts]</div>
        <div>minggu_normal_uas: $room[minggu_normal_uas]</div>
        <div>minggu_tenang_uas: $room[minggu_tenang_uas]</div>
        <div>durasi_uas: $room[durasi_uas]</div>
        <div>jeda_sesi: $room[jeda_sesi]</div>
      </div>
      <input type=hidden name=date_created value='$now'>
    ";
    $inputs = div_alert('success', "Sudah ada $jumlah_sesi sesi pada room ini.<hr>$info_room");
  }
} else {
  $inputs = "
    <div class=row>
      <div class=col-6>
        <div class=wadah>
        <div class='mb1'>Sesi untuk UTS</div>
        
        <select name='minggu_normal_uts' class='form-control mb2'>
          <option value='2'>Jumlah sesi UTS adalah 2 sesi (tanpa UTS)</option>
          <option value='3'>Jumlah sesi UTS adalah 3 sesi (tanpa UTS)</option>
          <option value='4'>Jumlah sesi UTS adalah 4 sesi (tanpa UTS)</option>
          <option value='5'>Jumlah sesi UTS adalah 5 sesi (tanpa UTS)</option>
          <option value='6'>Jumlah sesi UTS adalah 6 sesi (tanpa UTS)</option>
          <option value='7' selected>Jumlah sesi UTS adalah 7 sesi (tanpa UTS)</option>
          <option value='8'>Jumlah sesi UTS adalah 8 sesi (tanpa UTS)</option>
          <option value='10'>Jumlah sesi UTS adalah 10 sesi (tanpa UTS)</option>
          <option value='16'>Jumlah sesi UTS adalah 14 sesi (tanpa UTS)</option>
        </select>
  
        <select name='minggu_tenang_uts' class='form-control mb2'>
          <option value='0'>Tidak ada minggu tenang UTS</option>
          <option value='1' selected>Ada 1 minggu tenang Pra-UTS</option>
          <option value='2'>Ada 2 minggu tenang Pra-UTS</option>
        </select>
      
        <select name='durasi_uts' class='form-control mb2'>
          <option value='0'>Tidak ada UTS</option>
          <option value='1'>Durasi UTS selama 1 pekan</option>
          <option value='2' selected>Durasi UTS selama 2 pekan</option>
          <option value='3'>Durasi UTS selama 3 pekan</option>
          <option value='4'>Durasi UTS selama 4 pekan</option>
        </select>
        </div>
      </div>
  
  
      <div class=col-6>
        <div class=wadah>
        <div class='mb1'>Sesi untuk UAS</div>
        
        <select name='minggu_normal_uas' class='form-control mb2'>
          <option value='2'>Jumlah sesi UAS adalah 2 sesi (tanpa UAS)</option>
          <option value='3'>Jumlah sesi UAS adalah 3 sesi (tanpa UAS)</option>
          <option value='4'>Jumlah sesi UAS adalah 4 sesi (tanpa UAS)</option>
          <option value='5'>Jumlah sesi UAS adalah 5 sesi (tanpa UAS)</option>
          <option value='6'>Jumlah sesi UAS adalah 6 sesi (tanpa UAS)</option>
          <option value='7' selected>Jumlah sesi UAS adalah 7 sesi (tanpa UAS)</option>
          <option value='8'>Jumlah sesi UAS adalah 8 sesi (tanpa UAS)</option>
          <option value='10'>Jumlah sesi UAS adalah 10 sesi (tanpa UAS)</option>
          <option value='16'>Jumlah sesi UAS adalah 14 sesi (tanpa UAS)</option>
        </select>
  
        <select name='minggu_tenang_uas' class='form-control mb2'>
          <option value='0'>Tidak ada minggu tenang UAS</option>
          <option value='1' selected>Ada 1 minggu tenang Pra-UAS</option>
          <option value='2'>Ada 2 minggu tenang Pra-UAS</option>
        </select>
      
        <select name='durasi_uas' class='form-control mb2'>
          <option value='1'>Durasi UAS selama 1 pekan</option>
          <option value='2' selected>Durasi UAS selama 2 pekan</option>
          <option value='3'>Durasi UAS selama 3 pekan</option>
          <option value='4'>Durasi UAS selama 4 pekan</option>
        </select>
        </div>
  
      </div>
  
  
  
    </div>
    <div class='f12 abu'>
      <ul>
        <li>Jumlah sesi default adalah 7 kali pertemuan (tanpa UTS/UAS)</li>
        <li>Durasi UTS/UAS default selama 2 minggu</li>
        <li>Durasi total default adalah 18 minggu, room-closed pada minggu ke-19</li>
      </ul>
    </div>
  
    <select name='jeda_sesi' class='form-control'>
      <option value='1'>Sesi dilaksanakan setiap hari</option>
      <option value='2'>Sesi dilaksanakan setiap 2 hari</option>
      <option value='3'>Sesi dilaksanakan setiap 3 hari</option>
      <option value='4'>Sesi dilaksanakan setiap 4 hari</option>
      <option value='5'>Sesi dilaksanakan setiap 5 hari</option>
      <option value='6'>Sesi dilaksanakan setiap 6 hari</option>
      <option value='7' selected>Sesi dilaksanakan setiap minggu</option>
      <option value='30'>Sesi dilaksanakan setiap Bulan</option>
    </select>
    <div class='mt1 mb2 f12 abu ml2'>)* default jeda sesi adalah setiap minggu (7 hari). Tanggal sesi berikutnya otomatis diisi dengan +7 hari dari sesi sebelumnya </div>  
  ";
}
