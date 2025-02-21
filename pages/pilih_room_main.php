<?php
# =====================================================
# PROCESSOR PILIH ROOM
# =====================================================
if (isset($_POST['btn_pilih'])) {
  $_SESSION['dipa_id_room'] = $_POST['btn_pilih'];
  jsurl('?');
}
















# ============================================================
# MY TEMPORARY ROOMS TODAY
# ============================================================










# ============================================================
# MAIN SELECT ROOM
# ============================================================
if ($id_role == 1) {
  // $Room kelas _peserta
  $sub_sql_my_room = "SELECT 1  
  FROM tb_room_kelas p 
  JOIN tb_kelas q ON p.kelas=q.kelas  
  JOIN tb_kelas_peserta r ON q.kelas=r.kelas  
  WHERE p.id_room=a.id
  AND q.kelas='$kelas'
  AND r.id_peserta = $id_peserta";
} else {
  // $Room owner
  $sub_sql_my_room = "SELECT 1  
  FROM tb_room p 
  WHERE p.created_by = $id_peserta 
  AND p.id=a.id 
  ";
}

$s = "SELECT a.*,
a.nama as room,
a.status as status_room,
a.id as id_room,
b.nama as creator,
b.id as id_creator,
b.war_image as war_image_creator,
($sub_sql_my_room) my_room,
(
  SELECT id FROM tb_sesi 
  WHERE id_room=a.id 
  AND awal_presensi <= '$now'
  AND akhir_presensi > '$now') id_sesi_aktif 

FROM tb_room a 
JOIN tb_peserta b ON a.created_by=b.id  
WHERE 1 -- b.id = $id_peserta 
ORDER BY 
  my_room DESC, -- ROOM SAYA ATAU BUKAN 
  a.jenjang,
  a.jenis, -- INTI | TAMBAHAN | NONFORMAL
  a.no, -- NOMOR MAPEL/COURSE
  a.status DESC,
  a.nama  

LIMIT 50
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$div_my_rooms = '';
$my_rooms_sd = [];
$my_rooms_sd[1] = ''; // mapel inti
$my_rooms_sd[2] = ''; // mapel tambahan
$my_rooms_sd[3] = ''; // mapel informal
$other_room = '';
$my_jadwal_kelas = [];
$my_jadwal_harian = [];
while ($d = mysqli_fetch_assoc($q)) {

  if ($d['status_room'] == 100) {
    $btn_close_room = '';
    $jadwal_kelas_count = 0;
    if ($d['id_sesi_aktif']) {
      # ============================================================
      # GET JADWAL KELAS
      # ============================================================
      $s2 = "SELECT a.*,
      b.nama as nama_sesi,
      b.no, -- nomor sesi
      c.id as id_room, 
      c.nama as nama_room 
      
      FROM tb_sesi_kelas a 
      JOIN tb_sesi b ON a.id_sesi=b.id
      JOIN tb_room c ON b.id_room=c.id
      JOIN tb_kelas d ON a.kelas=d.kelas
      WHERE a.id_sesi = $d[id_sesi_aktif] 
      AND a.kelas != 'INSTRUKTUR' 
      AND d.ta = $ta_aktif
      ORDER BY a.jadwal_kelas 
      ";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      $jadwal_kelas_count = mysqli_num_rows($q2);

      while ($d2 = mysqli_fetch_assoc($q2)) {

        // debug
        if ($d['id'] == 33) {
          // echo '<pre>';
          // var_dump($d);
          // echo '</pre>';
          $d2['jadwal_kelas'] = '2025-02-21 19:20';
        }

        if (isset($my_jadwal_kelas[$d2['jadwal_kelas']])) {
          $id_sebelumnya = $my_jadwal_kelas[$d2['jadwal_kelas']]['id'];
          $kelas_sebelumnya = $my_jadwal_kelas[$d2['jadwal_kelas']]['kelas'];
          echo div_alert('warning', "Terdapat dua jadwal kelas yang sama [ $d2[jadwal_kelas] ] - [ $kelas_sebelumnya dan $d2[kelas] ], abaikan jika join kelas.");
        } else {
          // echo '<pre>';
          // var_dump("$d[nama] | $d[status]  | $d[id_sesi_aktif] | $d2[jadwal_kelas] ");
          // echo '</pre>';
          $my_jadwal_kelas[$d2['jadwal_kelas']] = $d2;

          $w = date('w', strtotime($d2['jadwal_kelas']));
          if (isset($my_jadwal_harian[$w])) {
            array_push($my_jadwal_harian[$w], $d2);
          } else {
            $my_jadwal_harian[$w][0] = $d2;
          }
        }
      }
    } else { // tidak ada sesi aktif
      # ============================================================
      # SARAN UNTUK CLOSING ROOM
      # ============================================================
      $btn_close_room = '<b class=red>CLOSE ROOM ZZZ</b>';
    }
    continue; // sudah di handle dengan UI2
    $status = 'Aktif';
    $gradasi = 'hijau';
    $btn = "<button class='btn btn-success mt2 w-100' name=btn_pilih value=$d[id_room]>Pilih $Room</button>";
  } elseif ($d['status_room'] < 0) {
    $status = 'Closed';
    $gradasi = 'kuning';
    $btn = "<button class='btn btn-warning mt2 w-100' name=btn_pilih value=$d[id_room] onclick='return confirm(`Pilih Closed $Room untuk melihat history?`)'>Closed</button>";
  } else {
    $status = 'Belum Aktif';
    $gradasi = 'merah';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(`$Room belum diaktifkan oleh $Trainer. Segera hubungi beliau via whatsApp!`)'>Inactive</span>";
    $btn = "<a href='?pilih_room&aktivasi_room=$d[id_room]' class='btn btn-secondary mt2 w-100' >Aktivasi</a>";
  }

  if ($id_room == $d['id_room']) {
    $wadah_active = 'wadah_active';
    $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(`Kamu sedang berada di $Room ini.`)'>Selected</span>";
  } else {
    $wadah_active = '';
  }

  if ($d['my_room']) {
    // $border = 'solid 3px blue';
  } else {
    $gradasi = 'abu';
    $border = '';
    $btn = "<a href='?pilih_room&daftar_ke_room=$d[id_room]' class='btn btn-secondary mt2 w-100' value=$d[id_room]>Daftar Anggota</a>";
  }

  $div_room = "
    <div class='col-md-4 col-lg-3'>
      <div class='wadah $wadah_active gradasi-$gradasi tengah' style='border: $border;'>
        <div class='darkblue f18'>$d[room]</div>
        <div class='red'>DEBUG: $d[id_sesi_aktif]</div>
        <div class=f12>Status: $status</div>
        <!-- img src='$lokasi_profil/$d[war_image_creator]' alt='pengajar' class='foto_profil' -->
        <div>By: $d[creator]</div>
        $btn
      </div>
    </div>
  ";
  if ($d['my_room']) {
    if ($d['jenjang'] == 'SD') {
      $my_rooms_sd[$d['jenis']] .= $div_room;
    } else {
      $div_my_rooms .= $div_room;
    }
  } else {
    $other_room .= $div_room;
  }
}
$div_my_rooms = $div_my_rooms ? $div_my_rooms : div_alert('warning tengah', $id_role == 2 ? "Anda belum punya $Room di TA $ta_show" : "Kamu belum dimasukan ke $Room manapun pada TA. $ta_show");
$link_buat_room_baru = $id_role == 2 ?  "<div class='alert alert-info'>$Room digunakan untuk mewadahi kegiatan belajar Anda dengan <b>multiple-kelas</b> dan dapat dipakai kembali (<b>reusable</b>) di setiap Tahun Ajar.</div> <div class='mb2'><a class='btn btn-primary w-100 ' href='?buat_room' onclick='return confirm(`Buat $Room Baru?`)'>Buat $Room Baru</a></div>" : '';

# ============================================================
# MY ROOMS SD
# ============================================================
$my_rooms_sd = (!$my_rooms_sd || $id_role != 2) ? '' : "
  <hr>
  <div class='wadah gradasi-toska'>
    <h4 class='tengah darkblue'>$Room Sekolah Dasar</h4>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Inti</div>
      <div class=row>
        $my_rooms_sd[1]
      </div>
    </div>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Tambahan</div>
      <div class=row>
        $my_rooms_sd[2]
      </div>
    </div>
    <div class='wadah bg-white'>
      <div class=' darkred f24 miring mb2'>Mapel Informal</div>
      <div class=row>
        $my_rooms_sd[3]
      </div>
    </div>
  </div>
";

# ============================================================
# UI2
# ============================================================
// echo '<pre>';
// var_dump($my_jadwal_harian);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;

ksort($my_jadwal_harian);
$arr_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$ui2 = '';
for ($w = 1; $w <= 6; $w++) { // dari senin s.d sabtu 
  $my_jadwal = $my_jadwal_harian[$w] ?? [];
  // $count_sesi = 0;
  $div_my_jadwal = '';
  if ($my_jadwal) {
    // $count_sesi = count($my_jadwal);
    foreach ($my_jadwal as $k => $v) {
      // echo '<pre>';
      // var_dump($v);
      // echo '</pre>';

      $jadwal_kelas = date('d-M, H:i', strtotime($v['jadwal_kelas']));
      $selisih =   strtotime($v['jadwal_kelas']) - strtotime('now');

      // default durasi 90 menit zzz
      $durasi = 90;
      $info = '';
      $eta = '';
      $Ganti = 'Ganti';
      $btn_type = 'success';
      $wadah_active = '';
      if ($selisih > 0) {
        # ============================================================
        # BELUM MULAI
        # ============================================================
        $info = 'BELUM MULAI';
        $Ganti = 'Next';
        $btn_type = 'info';
        # ============================================================
        # CREATE TIMER
        # ============================================================
        $timer = '';
        if (!$timer) { // hanya satu timer ke sesi terdekat
          include 'timer.php';
          $info .= "$timer";
        }
      } elseif ($selisih <= 0 and $selisih > -$durasi * 60) {
        # ============================================================
        # SEDANG PERKULIAHAN
        # ============================================================
        $info = '<b class="blue f18">SEDANG PERKULIAHAN</b>';
        $wadah_active = 'wadah_active';
      } else {
        # ============================================================
        # SUDAH USAI
        # ============================================================
        $info = 'SUDAH USAI';
        $eta = eta2($v['jadwal_kelas']);
        $Ganti = 'Review';
      }


      if ($id_room == $v['id_room']) {
        $btn = "<span class='btn btn-secondary mt2 w-100' onclick='alert(`Anda sedang berada di Room ini.`)'>Selected</span>";
      } else {
        $btn = "<button class='btn btn-$btn_type mt2 w-100' name=btn_pilih value=$v[id_room]>$Ganti $Room</button>";
      }

      $debug = "$v[id_room]";

      $div_my_jadwal .= "
        <div class='col-md-4 col-lg-3'>
          <div class='wadah $wadah_active gradasi-$gradasi tengah' style='border: $border;'>
            <div class=' f12 blue bold'>$v[nama_room] $debug</div>
            <div class='darkblue f18'>P$v[no] $v[nama_sesi]</div>
            <div class='f12 abu'>$v[kelas]</div>
            <div class='f12 abu'>$jadwal_kelas</div>
            <div class='f12 brown'>$eta</div>
            <div class='f12 brown'>$info</div>
            $btn
          </div>
        </div>
      ";
      $tanggal = date('d M Y', strtotime($v['jadwal_kelas']));
    }



    $ui2 .= "
      <div>
        <div class='tengah gradasi-kuning p2 mb2 border-top blue bold'>
          $arr_hari[$w], $tanggal
        </div>
        <div class='row mb4'>
          $div_my_jadwal
        </div>
      </div>
    ";
  }
}

// echo '<pre>';
// var_dump($my_jadwal_harian);
// echo '<b style=color:red>DEBUGING: echopreExit</b></pre>';
// exit;

# ============================================================
# FINAL ECHO
# ============================================================
echo "
<div class=container>
  <form method=post>
    <hr>
    <h3 class='darkblue f20 upper tengah mb4'>$Room Aktif $ta_show</h3>
    $ui2
    <div class=row>
      $div_my_rooms
    </div>
    $my_rooms_sd
    <hr>
    <div class='tengah'>
      $link_buat_room_baru
      <a href='?logout' onclick='return confirm(`Logout?`)'>Logout</a>
    </div>
    <hr>
    <h3 class='mt4 mb4 tengah'>$Room $Trainer lain</h3>
    <div class=row>
      $other_room
    </div>
  </form>
</div>
";
