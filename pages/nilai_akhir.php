<?php
login_only();
include 'nilai_akhir-functions.php';
$get_save = $_GET['save'] ?? '';
$get_show_img = $_GET['show_img'] ?? '';
$get_csv = $_GET['csv'] ?? null;
$get_csv = $id_role == 1 ? null : $get_csv;
$get_params = '';
foreach ($_GET as $key => $value) {
  if ($key == 'nilai_akhir') continue;
  $get_params .= "&$key=$value";
}



$judul = 'Nilai Akhir';
set_title($judul);


$show_profile_peserta = '';
if ($id_role == 2) {
  $show_img = $get_show_img ? 0 : 1;
  $Show = $get_show_img ? 'Hide' : 'Show';
  $show_profile_peserta = "<a href='?nilai_akhir&show_img=$show_img'>$Show Profil Peserta</a>";
}

echo "
<div class='section-title' data-aos='fade'>
  <h2>$judul</h2>
  <p>Kalkulasi Poin Pembelajaran dan Nilai Akhir</p>
  $show_profile_peserta
</div>";





# =======================================================
# INITIAL VARIABLE
# =======================================================
$belum = '<span class="consolas darkred f12 miring">belum</span>';
$img['delete'] = '<img class=zoom src="assets/img/icon/delete.png" height=25px />';







# =======================================================
# PEMBOBOTAN DAN HEADER TABEL
# =======================================================
// ZZZ CUSTOMIZE BOBOT FROM DB
$rbobot = [];

$rbobot['count_presensi_offline'] = 0;
$rbobot['count_presensi_online'] = 6;
$rbobot['count_ontime'] = 5;
$rbobot['count_latihan'] = 5;
$rbobot['count_latihan_wajib'] = 10;
$rbobot['count_challenge'] = 5;
$rbobot['count_challenge_wajib'] = 10;
$rbobot['rank_room'] = 12;
$rbobot['rank_kelas'] = 17;
$rbobot['nilai_uts'] = 15;
$rbobot['nilai_uas'] = 15;
$rbobot['nilai_remed_uts'] = 0;
$rbobot['nilai_remed_uas'] = 0;

$rlink['count_presensi_offline'] = '?presensi';
$rlink['count_presensi_online'] = '?presensi';
$rlink['count_ontime'] = '?presensi';
$rlink['count_latihan'] = '?activity&jenis=latihan';
$rlink['count_latihan_wajib'] = '?activity&jenis=latihan';
$rlink['count_challenge'] = '?activity&jenis=challenge';
$rlink['count_challenge_wajib'] = '?activity&jenis=challenge';
$rlink['rank_room'] = '?grades';
$rlink['rank_kelas'] = '?grades';
$rlink['nilai_uts'] = '?ujian';
$rlink['nilai_uas'] = '?ujian';
$rlink['nilai_remed_uts'] = '?ujian';
$rlink['nilai_remed_uas'] = '?ujian';

$td_bobot = '';
$th_komponen = '';
$total_bobot = 0;
foreach ($rbobot as $komponen => $bobot) {
  $rkonversi[$komponen] = 0;
  $rvalue_of[$komponen] = 'of ' . $unset;
  $rgradasi[$komponen] = '';
  $komponen = str_replace('_', ' ', $komponen);
  $total_bobot += $bobot;
  $th_komponen .= "<th>$komponen</th>";
  $td_bobot .= "<td>$bobot%</td>";
}

$tr = '';
$thead = "
  <thead class='f12'>
    <th width=4%>No</th>
    <th width=31%>Nama</th>
    $th_komponen
    <th>Nilai Akhir</th>
  </thead>
  <tr class='f12 abu miring'>
    <td colspan=2>Bobot</td>
    $td_bobot
    <td>$total_bobot%</td>
  </tr>
";

# =======================================================
# PRE SELECT | TOTAL LATIHAN | CHALLENGE
# =======================================================
// $s = "";
























# =======================================================
# MAIN SELECT | LIST PESERTA
# =======================================================
$sql_id_peserta = $id_role == 1 ? "a.id=$id_peserta" : '1';
$nama_paket_soal_uts = 'Soal UTS Semester 1 TA. 2023/2024';
$nama_paket_soal_uas = 'Soal UAS 2023';
$nama_paket_soal_remed_uts = 'Soal Pasca UTS';
$nama_paket_soal_remed_uas = 'Soal Remed UAS';

$from_tb_jawabans = "FROM tb_jawabans p 
  JOIN tb_paket_kelas q ON p.paket_kelas=q.paket_kelas 
  JOIN tb_paket r ON q.id_paket=r.id 
  WHERE p.id_peserta=a.id 
  AND p.id_room=$id_room 
  AND r.nama ";


$s = "SELECT  
a.id as id_peserta,
a.nama as nama_peserta,
a.status as status_peserta,
a.nim,
a.username,
a.war_image,
b.kelas,
b.*,
c.*,

-- ========================================
-- SELECT PRESENSI SUMMARY
-- ========================================
(
  SELECT jumlah_presensi_offline 
  FROM tb_presensi_summary 
  WHERE id_peserta=a.id 
  AND id_room=$id_room) count_presensi_offline,
(
  SELECT jumlah_presensi 
  FROM tb_presensi_summary 
  WHERE id_peserta=a.id 
  AND id_room=$id_room) count_presensi_online,
(
  SELECT jumlah_ontime 
  FROM tb_presensi_summary 
  WHERE id_peserta=a.id 
  AND id_room=$id_room) count_ontime,




-- ========================================
-- SELECT TOTAL PESERTA
-- ========================================
(
  SELECT count(1) FROM tb_room_kelas p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  JOIN tb_kelas_peserta r ON q.kelas=r.kelas
  JOIN tb_peserta s ON r.id_peserta=s.id 
  WHERE q.ta=$ta_aktif 
  AND s.id_role = 1 
  AND s.status = 1 
  AND p.id_room=$id_room) total_peserta,
(
  SELECT count(1) FROM tb_kelas_peserta p  
  JOIN tb_kelas q ON p.kelas=q.kelas  
  JOIN tb_peserta r ON p.id_peserta=r.id 
  WHERE q.ta=$ta_aktif 
  AND r.id_role = 1 
  AND r.status = 1 
  AND q.kelas=c.kelas) total_peserta_kelas,

-- ========================================
-- SELECT LATIHAN AND CHALLENGE
-- ========================================
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=d.id) count_latihan,
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=d.id
  AND q.is_wajib is not null) count_latihan_wajib,
(
  SELECT COUNT(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=d.id) count_challenge,
(
  SELECT COUNT(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=d.id
  AND q.is_wajib is not null) count_challenge_wajib,




-- ========================================
-- SELECT UTS | UAS 
-- ========================================
(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_uts'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uts, 
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_uts'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_uts, 
(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_uas'
  ORDER BY p.nilai DESC LIMIT 1) nilai_uas ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_uas'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_uas ,
(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_remed_uts'
  ORDER BY p.nilai DESC LIMIT 1) nilai_remed_uts ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_remed_uts'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_remed_uts, 
(
  SELECT p.nilai $from_tb_jawabans = '$nama_paket_soal_remed_uas'
  ORDER BY p.nilai DESC LIMIT 1) nilai_remed_uas ,
(
  SELECT p.tanggal_submit $from_tb_jawabans = '$nama_paket_soal_remed_uas'
  ORDER BY p.nilai DESC LIMIT 1) tanggal_submit_remed_uas, 

-- ========================================
-- SELECT UTS | UAS MANUAL 
-- ========================================
(
  SELECT uts FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) uts_manual,
(
  SELECT uas FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) uas_manual,




-- ========================================
-- MY POIN DATA TEMPORER
-- ========================================
(
  SELECT rank_room FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_room,
(
  SELECT rank_kelas FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_kelas


FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
-- WHERE a.status=1 -- _peserta aktif only
WHERE 1 -- _peserta aktif/nonaktif
AND password is not null -- _peserta aktif pasti sudah ganti pass
AND a.id_role=1 -- _peserta only tidak GM
AND $sql_id_peserta -- SWITCH VIEW PESERTA | GM
AND c.ta = $ta_aktif 
AND d.id_room = $id_room 


ORDER BY b.kelas, a.nama
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_data = mysqli_num_rows($q);
if (mysqli_num_rows($q) > 1 and $id_role == 1) die("Duplicate result ditemukan untuk setiap $Peserta");
if ($id_role == 2 and !$total_data) {
  echo div_alert('danger', "Belum ada $Peserta pada $Room ini. | <a href='?peserta_kelas'>Peserta Kelas</a>");
  jsurl('?peserta_kelas', 5000);
  exit;
}







































if ($get_csv) {
  # =======================================================
  # CSV FILE HANDLER STARTED
  # =======================================================
  $date = date('ymd');
  $src_csv = "csv/nilai-akhir-" . strtolower(str_replace(' ', '_', $nama_room)) . "-$date.csv";
  $file = fopen($src_csv, "w+");
  fputcsv($file, ['NILAI AKHIR ' . strtoupper($nama_room)]);
  fputcsv($file, ['Tanggal: ' . date('d-M-Y')]);
  fputcsv($file, ['Jam: ' . date('H:i')]);
  fputcsv($file, [' ']);
}


# =======================================================
# LOOPING MAIN SELECT
# =======================================================
$tr_empty = '<tr><td colspan=100%>&nbsp;</td></tr>';
$no = 0;
$i = 0;
$last_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {

  if (!$d['total_peserta_kelas']) {
    die("Peserta kelas tidak boleh 0. Kelas: $d[kelas]");
  }
  // exception for temporer rank at tb_poin
  if ($d['rank_room'] > $d['total_peserta']) $d['rank_room'] = $d['total_peserta'];

  $i++;
  $no++;
  $d['nomor'] = $i;
  $d['nama_peserta'] = strtoupper($d['nama_peserta']);

  // handler blok tiap kelas
  $kelas_ini = $d['kelas'];
  if ($last_kelas != $kelas_ini and $i != 1) $tr .= $tr_empty;
  if ($last_kelas != $kelas_ini) {
    $tr .= $thead;
    $no = 1;
  }














  # ======================================================
  # rvalue_of HANDLER
  # ======================================================

  // inject from room_vars
  $d['total_count_presensi_offline'] = $room_count['count_presensi_aktif'];
  $d['total_count_presensi_online'] = $room_count['count_presensi_aktif'];
  $d['total_count_ontime'] = $room_count['count_presensi_aktif'];
  $d['total_count_latihan'] = $total_latihan;
  $d['total_count_latihan_wajib'] = $total_latihan_wajib;
  $d['total_count_challenge'] = $total_challenge;
  $d['total_count_challenge_wajib'] = $total_challenge_wajib;
  $d['total_rank_room'] = $d['total_peserta'];
  $d['total_rank_kelas'] = $d['total_peserta_kelas'];
  $d['total_nilai_uts'] = 100;
  $d['total_nilai_uas'] = 100;
  $d['total_nilai_remed_uts'] = 100;
  $d['total_nilai_remed_uas'] = 100;

  foreach ($rbobot as $key => $value) {
    $my_value[$key] = $d[$key] ?? 0; // or zero
    if ($key == 'nilai_uts' and !$d[$key]) $my_value[$key] = $d['uts_manual'];
    if ($key == 'nilai_uas' and !$d[$key]) $my_value[$key] = $d['uas_manual'];
    $rtotal_value[$key] = $d['total_' . $key];
    if (strpos("salt$key", 'nilai_')) {
      $rvalue_of[$key] = $my_value[$key];
    } else {
      $rvalue_of[$key] = "$my_value[$key] <span class=f12>of $rtotal_value[$key]</span>";
    }
  }

  # =======================================================
  # KONVERSIKAN
  # =======================================================
  $rkonversi['count_presensi_offline'] = konversikan($d['count_presensi_offline'], $d['total_count_presensi_offline']);
  $rkonversi['count_presensi_online'] = konversikan($d['count_presensi_online'], $d['total_count_presensi_online']);
  $rkonversi['count_ontime'] = konversikan($d['count_ontime'], $d['total_count_ontime']);
  $rkonversi['count_latihan'] = konversikan($d['count_latihan'], $d['total_count_latihan']);
  $rkonversi['count_latihan_wajib'] = konversikan($d['count_latihan_wajib'], $d['total_count_latihan_wajib']);
  $rkonversi['count_challenge'] = konversikan($d['count_challenge'], $d['total_count_challenge']);
  $rkonversi['count_challenge_wajib'] = konversikan($d['count_challenge_wajib'], $d['total_count_challenge_wajib']);


  if ($d['rank_room']) {
    $rkonversi['rank_room'] = round(110 - (($d['rank_room'] - 1) * ((round($d['total_peserta'] * 8 / 10, 0) / $d['total_peserta']) * (100 / $d['total_peserta']))), 0);
    if ($rkonversi['rank_room'] > 100) $rkonversi['rank_room'] = 100;
  } else {
    $rkonversi['rank_room'] = 0;
  }

  if ($d['rank_kelas']) {
    $rkonversi['rank_kelas'] = round(110 - (($d['rank_kelas'] - 1) * ((round($d['total_peserta_kelas'] * 8 / 10, 0) / $d['total_peserta_kelas']) * (100 / $d['total_peserta_kelas']))), 0);
    if ($rkonversi['rank_kelas'] > 100) $rkonversi['rank_kelas'] = 100;
  } else {
    $rkonversi['rank_kelas'] = 0;
  }

  $rkonversi['nilai_uts'] = $d['nilai_uts'] ?? $d['uts_manual'];
  $rkonversi['nilai_remed_uts'] = $d['nilai_remed_uts'];
  $rkonversi['nilai_uas'] = $d['nilai_uas'] ?? $d['uas_manual'];
  $rkonversi['nilai_remed_uas'] = $d['nilai_remed_uas'];
  # =======================================================
  # NILAI AKHIR BY LOOP CALCULATIONS FOR EVERYONE
  # =======================================================
  $td = '';
  $nilai_akhir = 0;
  $bobot_penyesuaian = 0;
  foreach ($rbobot as $key => $value) {
    $kolom = str_replace('_', ' ', $key);
    if (strpos("salt$key", 'nilai_')) {
      $dikali_bobot = "$rbobot[$key]%";
    } else {
      $dikali_bobot = "$rkonversi[$key]x$rbobot[$key]%";
    }

    $sub_nilai_akhir = round(($rkonversi[$key] * $rbobot[$key]) / 100, 2);
    $nilai_akhir += $sub_nilai_akhir;
    $gradasi = $rbobot[$key] ?  gradasi_nilai($rkonversi[$key], $awal_nilai) : '';

    if ($key == 'nilai_uts' and !$room_count['sudah_uts']) {
    } elseif ($key == 'nilai_uas' and !$room_count['sudah_uas']) {
    } else {
      $bobot_penyesuaian += $rbobot[$key];
    }

    $td .= "
      <td class='gradasi-$gradasi'>
        <div class=hideit>$kolom:</div>
        $rvalue_of[$key]
        <div class='miring abu f10'>$dikali_bobot</div>
        <div class='darkblue'>$sub_nilai_akhir</div>
      </td>
    ";
  }

  # ============================================================
  # FIKSASI NILAI AKHIR
  # ============================================================
  if ($nilai_akhir > 100) $nilai_akhir = 100;
  $nilai_akhir = round($nilai_akhir * $total_bobot / $bobot_penyesuaian, 2);
  $nilai_akhir = $nilai_akhir > 100 ? 100 : $nilai_akhir;

  if ($get_csv) {
    # ============================================================
    # NILAI AKHIR FOR CSV
    # ============================================================
    $d[' '] = ' ';
    foreach ($rkonversi as $key_konversi => $value_konversi) {
      $d['KONVERSI ' . $key_konversi] = $value_konversi;
    }
    $d['  '] = '  ';
    $d['nilai_akhir'] = $nilai_akhir;

    # ============================================================
    # CSV HEADER HANDLER
    # ============================================================
    if ($i == 1) {
      $rheader_csv = [];
      foreach ($d as $nama_kolom => $array_data) {
        array_push($rheader_csv, strtoupper(str_replace('_', ' ', $nama_kolom)));
      }
      fputcsv($file, $rheader_csv);
    }

    # ============================================================
    # CSV KONTEN HANDLER
    # ============================================================
    fputcsv($file, $d);
  }


















  # =======================================================
  # FINAL TR
  # =======================================================
  if ($id_role == 2) {
    $gradasi =   gradasi_nilai($nilai_akhir, $awal_nilai);


    if ($get_save) {
      // auto-save for everyone
      $s2 = "UPDATE tb_poin SET nilai_akhir=$nilai_akhir WHERE id_peserta=$d[id_peserta] AND id_room=$id_room";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    } else {
      if ($get_show_img) {
        $src = "$lokasi_profil/$d[war_image]";
        if (file_exists($src)) {
          $img = "<img src='$src' class='foto_profil'>";
        } else {
          $img = '<span class="f12 abu consolas">belum ada profil</span>';
        }
      } else {
        $img = '';
      }

      $status_show = $d['status_peserta'] ? '' : '<div class="pt2"><span class="f12 p1 bg-red white br5">non-aktif</span></div>';

      // preview to table
      $tr .= "
      <tr class='f14'>
        <td>$no</td>
        <td>
          $d[nama_peserta] 
          <a href='?login_as&username=$d[username]'>$img_login_as</a>
          <div class='kecil miring abu'>$d[kelas]</div>
          $status_show 
          $img
        </td>
        $td
        <td class='gradasi-$gradasi'>$nilai_akhir</td>
      </tr>";
    }
  }

  //for repeat header
  $last_kelas = $kelas_ini;
}


if ($get_csv) {
  # ============================================================
  # CSV CLOSING HANDLER
  # ============================================================
  fputcsv($file, [
    'DATA FROM: Gamified Learning DIPA Joiner, PRINTED AT: ' .
      date('F d, Y, H:i:s')
  ]);
  fclose($file);
  $link_download_export_csv = " 
    <a href='$src_csv' target=_blank class='btn btn-primary my-3'>Download Nilai Akhir</a>
    <div class=mt-2> <a href='?nilai_akhir' >Back to Rekap Nilai Akhir</a> </div>
  ";
} else {
  $link_download_export_csv = "<a href='?nilai_akhir&csv=1' class='btn btn-success ' onclick='alert(`Export Data?`)'>Export Data</a>";
}


// return to normal view
if ($get_save) {
  echo div_alert('success', "Data Nilai Akhir sudah tersimpan.<hr><a class='btn btn-primary' href='?nilai_akhir'>Kembali ke Mode View Nilai Akhir</a>");
  exit;
}


if ($id_role == 2) {
  if ($get_csv) {
    $blok_kelas = $link_download_export_csv;
  } else {
    $blok_kelas = "
      <table class='table'>
        $tr
      </table>
      <div class=mb4>
        <a href='?nilai_akhir&save=1' class='btn btn-primary ' onclick='alert(`Setelah Anda menyimpan data Nilai Akhir sebaiknya Anda mengumumkan kepada para $Peserta.`)'>Simpan Data Nilai Akhir untuk $total_data Peserta</a>
        $link_download_export_csv
      </div>
    ";
  }
} else {
  include 'nilai_akhir-show_single.php';
}









$jumlah_peserta_show = $id_role == 1 ? '' : "<div class=mb2>Jumlah Peserta: $total_data $Peserta</div>";
echo "
  <div data-aos=fade>
    $jumlah_peserta_show
    <div>$room[info_ujian]</div>
    $blok_kelas
  </div>
";
?>





















<script>
  $(function() {
    $('.delete_peserta').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let id_peserta = rid[1];

      let link_ajax = "ajax/ajax_delete_peserta.php?id_peserta=" + id_peserta;
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            console.log(a);
          } else {
            alert(a);
          }
        }
      })
    })
  })
</script>