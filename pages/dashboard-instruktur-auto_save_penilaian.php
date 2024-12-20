<?php
echo div_alert('info', 'Belum ada penilaian instruktur pada minggu ini<hr>Perform Auto-calculations... please wait');

$s = "SELECT * FROM tb_penilaian_instruktur";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = 0;
$arr_multiplier = [];
$arr_point = [];
$total_poin = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $my_multiplier = '';
  $my_point = 0;


  $id_penilaian = $d['id'];
  $penilaian = $d['penilaian'];
  $basic_poin = $d['poin'];
  $satuan = $d['satuan'] ? key2kolom($d['satuan']) : key2kolom($d['deskripsi']);
  $s2 = '';
  if ($penilaian == 'count_learning_path') {
    $s2 = "SELECT 
    COALESCE(a.status_kelengkapan,0) as status_kelengkapan
    FROM tb_sesi a 
    JOIN tb_room b ON a.id_room=b.id 
    WHERE a.jenis=1 -- sesi normal
    AND b.status = 100 -- active room
    AND b.created_by = $id_peserta -- milik sendiri 
    AND b.id = $id_room 
    ";
  } elseif ($penilaian == 'count_peserta') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_room_kelas b ON a.id=b.id_room 
    JOIN tb_kelas c ON b.kelas=c.kelas 
    JOIN tb_kelas_peserta d ON c.kelas=d.kelas 
    JOIN tb_peserta e ON d.id_peserta=e.id 
    WHERE a.created_by = $id_peserta -- milik sendiri 
    AND a.status = 100 -- active room 
    AND a.id = $id_room
    AND c.status = 1 -- kelas aktif 
    AND e.status = 1 -- _peserta aktif
    AND e.id_role = 1 -- _peserta only
    ";
  } elseif ($penilaian == 'count_latihan') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_latihan b ON a.id=b.id_room 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND b.ket is not null -- prosedur latihan sudah di update 
    AND a.id=$id_room 
    ";
  } elseif ($penilaian == 'count_challenge') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_challenge b ON a.id=b.id_room 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND b.ket is not null -- prosedur challenge sudah di update
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_sublevel_challenge') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_challenge b ON a.id=b.id_room 
    JOIN tb_sublevel_challenge c ON b.id=c.id_challenge 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND b.ket is not null -- prosedur challenge sudah di update
    AND c.objective is not null -- sublevel sudah di update
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_verif_latihan') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi b ON a.id=b.id_room 
    JOIN tb_assign_latihan c ON b.id=c.id_sesi  
    JOIN tb_bukti_latihan d ON c.id=d.id_assign_latihan   
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND d.tanggal_verifikasi is not null 
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_verif_challenge') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi b ON a.id=b.id_room 
    JOIN tb_assign_challenge c ON b.id=c.id_sesi  
    JOIN tb_bukti_challenge d ON c.id=d.id_assign_challenge   
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND d.tanggal_verifikasi is not null 
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_presensi_ontime') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi b ON a.id=b.id_room 
    JOIN tb_presensi c ON c.id_sesi=b.id 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND c.is_ontime = 1 
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_presensi_offline') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi b ON a.id=b.id_room 
    JOIN tb_presensi_offline c ON c.id_sesi=b.id 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_tanam_soal') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi b ON a.id=b.id_room 
    JOIN tb_soal_peserta c ON c.id_sesi=b.id 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND (c.id_status IS NULL OR c.id_status >= 0)
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_play_quiz') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_war b ON a.id=b.id_room 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND b.id_penjawab != $id_peserta -- bukan dirinya 
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_bertanya') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_room_kelas b ON a.id=b.id_room 
    JOIN tb_bertanya c ON b.id=c.id_room_kelas 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    -- AND (c.verif_status is null OR verif_status != -1) -- bukan dirinya 
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_soal_ujian') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_soal b ON a.id=b.id_room 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_paket_soal') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi s ON a.id=s.id_room 
    JOIN tb_paket b ON b.id=b.id_sesi 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND a.id = $id_room
    ";
  } elseif ($penilaian == 'count_attemp_ujian') {
    $s2 = "SELECT 1 FROM tb_room a 
    JOIN tb_sesi s ON a.id=s.id_room 
    JOIN tb_paket b ON s.id=b.id_sesi 
    JOIN tb_paket_kelas c ON b.id=c.id_paket 
    JOIN tb_jawabans d ON d.paket_kelas=c.paket_kelas 
    WHERE a.created_by = $id_peserta -- milik sendiri  
    AND a.status = 100 -- active room
    AND a.id = $id_room
    ";
  } else {
    $redirect_show = key2kolom($d['redirect_to']);
    $link_redirect = $d['redirect_to'] ? "| <a href='?$d[redirect_to]'>$redirect_show</a>" : '';
    $my_multiplier .= div_alert('danger', "Belum ada kalkulasi untuk penilaian <u class='darkblue'>$penilaian</u> $link_redirect");
  }

  if ($s2) {
    $arr_multiplier[$id_penilaian] =  '';
    echo "<hr>$s2";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q2)) {
      $my_multiplier .= div_alert('danger', "Belum ada data $satuan di semua room Anda");
    } else {
      $count = mysqli_num_rows($q2);
      $my_multiplier .= "<div>$count $satuan $img_check</div>";
      if ($penilaian == 'count_learning_path') {
        $arr_status[0] = 0;
        $arr_status[1] = 0;
        $arr_status[2] = 0;
        $arr_status[3] = 0;
        $arr_status[4] = 0;
        while ($d2 = mysqli_fetch_assoc($q2)) {
          $my_point += ($d2['status_kelengkapan'] + 1) * $basic_poin;
          $arr_status[$d2['status_kelengkapan']]++;
        }

        $li = '';
        foreach ($arr_status as $key => $value) {
          if ($value) {
            $key_plus = $key + 1;
            $sum = $value * $key_plus * $basic_poin;
            $li .= "<li>Status $key : $value x $key_plus x $basic_poin = $sum</li>";
          }
        }
        if ($li) $my_multiplier .= "<ul class='abu f12 miring'>$li</ul>";
      } else {
        $my_point += $count * $basic_poin;
      }
      $arr_multiplier[$id_penilaian] =  "$count $satuan";
    }

    $arr_point[$id_penilaian] =  $my_point;
    $total_poin += $my_point;
  }

  $penilaian_show = key2kolom($penilaian);

  $tr .= "
    <tr>
      <td>$i</td>
      <td>
        <div class=darkblue>$penilaian_show</div>
        <div class='darkabu miring f14 mb1 mt1'>$d[deskripsi]</div>
        <div class='abu f12'>$d[multiplier_info]</div>
      </td>
      <td class='tengah desktop_only'>$basic_poin TP</td>
      <td class='desktop_only'>$my_multiplier</td>
      <td>$my_point TP</td>
    </tr>
  ";
} // end while penilaian

$total_poin_show = number_format($total_poin);

if (!isset($thead)) $thead = "
  <thead class='gradasi-toska'>
    <th>No</th>
    <th>Detail Penilaian Instruktur</th>
    <th class='tengah desktop_only'>Basic Poin</th>
    <th class='desktop_only'>My Multiplier Info</th>
    <th class=kanan>Teaching Points</th>
  </thead>
";

echo "
  <table class=table>
    $thead
    $tr
  </table>
  <div class='gradasi-toska tengah p4'>
    <div class=f20>Total Teaching Point (TP) :</div>
    <div class=f40>$total_poin_show TP</div>
  </div>
";

foreach ($arr_point as $id_penilaian => $value) {
  $kode = "$id_penilaian-$id_peserta-$id_room-$week";
  $multiplier_info_or_null = $arr_multiplier[$id_penilaian] ? "'$arr_multiplier[$id_penilaian]'" : 'NULL';
  if ($value) { // save hanya yang bernilai
    $s = "INSERT INTO tb_penilaian_weekly (
      kode, 
      id_penilaian,
      id_instruktur,
      id_room,
      week,
      my_multiplier_info,
      my_point
    ) VALUES (
      '$kode', 
      '$id_penilaian',
      $id_peserta,
      $id_room,
      $week,
      $multiplier_info_or_null,
      $value
    ) ON DUPLICATE KEY UPDATE 
      id_penilaian = '$id_penilaian',
      id_instruktur = $id_peserta,
      week = $week,
      my_multiplier_info = $multiplier_info_or_null,
      my_point = $value
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echolog("updating $penilaian");
  }
}

echo div_alert('success', 'Auto-Update Point Mingguan Instruktur sukses.');
jsurl('?', 1000);
