<?php
# ============================================================
# AKTIVASI JUMLAH SESI
# ============================================================
$inputs = "
  <div class=row>
    <div class=col-6>
      <div class=wadah>
      <div class='mb1'>Sesi untuk UTS</div>
      
      <select name='jumlah_sesi_uts' class='form-control mb2'>
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
        <option value='1'>Durasi UTS selama 1 minggu</option>
        <option value='2' selected>Durasi UTS selama 2 minggu</option>
        <option value='3'>Durasi UTS selama 3 minggu</option>
        <option value='4'>Durasi UTS selama 4 minggu</option>
      </select>
      </div>
    </div>


    <div class=col-6>
      <div class=wadah>
      <div class='mb1'>Sesi untuk UAS</div>
      
      <select name='jumlah_sesi_uas' class='form-control mb2'>
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
        <option value='1'>Durasi UAS selama 1 minggu</option>
        <option value='2' selected>Durasi UAS selama 2 minggu</option>
        <option value='3'>Durasi UAS selama 3 minggu</option>
        <option value='4'>Durasi UAS selama 4 minggu</option>
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
