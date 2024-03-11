<style>
  th {
    background: linear-gradient(#cfc, #afa)
  }
</style>
<?php
login_only();

function gradasi($nilai)
{
  if ($nilai) {
    if ($nilai >= 85) {
      return  'hijau';
    } elseif ($nilai >= 70) {
      return  'toska';
    } elseif ($nilai >= 50) {
      return  'kuning';
    } else {
      return  'merah';
    }
  } else {
    if ($nilai === 0) {
      return 'merah';
    } else {
      return '';
    }
  }
}
function konversikan($count, $total)
{
  if ($count == 0 || $total == 0) {
    return 0;
  } elseif ($count == 1 and $total == 1) {
    return 100;
  } elseif ($count == $total) {
    return 100;
  } else {
    $hasil = round(50 + ($count - 1) * ((round($total * 8 / 10, 0) / $total) * (100 / $total)), 0);
    return $hasil > 100 ? 100 : $hasil;
  }
}

$judul = 'Nilai Akhir';
set_title($judul);




echo "
<div class='section-title' data-aos='fade'>
  <h2>$judul</h2>
  <p>Kalkulasi Poin Pembelajaran dan Nilai Akhir</p>
</div>";





# =======================================================
# INITIAL VARIABLE
# =======================================================
$belum = '<span class="consolas darkred f12 miring">belum</span>';
$img['delete'] = '<img class=zoom src="assets/img/icons/delete.png" height=25px />';







# =======================================================
# PEMBOBOTAN DAN HEADER TABEL
# =======================================================
$rbobot['count_presensi_offline'] = 3;
$rbobot['count_presensi_online'] = 15;
$rbobot['count_ontime'] = 10;
$rbobot['count_latihan'] = 5;
$rbobot['count_latihan_wajib'] = 25;
$rbobot['count_challenge'] = 5;
$rbobot['count_challenge_wajib'] = 3;
$rbobot['rank_global'] = 15;
$rbobot['rank_kelas'] = 25;
$rbobot['nilai_uts'] = 0;
$rbobot['nilai_uas'] = 0;
$rbobot['nilai_remed_uts'] = 0;
$rbobot['nilai_remed_uas'] = 0;

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
  JOIN tb_paket_soal q ON p.id_paket_soal=q.id 
  WHERE p.id_peserta=a.id 
  AND p.id_room=$id_room 
  AND q.nama ";

$s = "SELECT  
a.id as id_peserta,
a.nama as nama_peserta,
a.nim,
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
-- SELECT SESI AKTIF
-- ========================================
(
  SELECT COUNT(1) FROM tb_sesi p 
  JOIN tb_sesi_kelas q ON p.id=q.id_sesi 
  WHERE p.id_room=$id_room 
  AND p.awal_presensi <= '$now') count_sesi_aktif,



-- ========================================
-- SELECT LATIHAN AND CHALLENGE
-- ========================================
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas) count_latihan,
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas
  AND q.is_wajib is not null) count_latihan_wajib,
(
  SELECT COUNT(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas) count_challenge,
(
  SELECT COUNT(1) FROM tb_bukti_challenge p 
  JOIN tb_assign_challenge q ON p.id_assign_challenge=q.id  
  WHERE p.id_peserta=a.id 
  AND q.id_room_kelas=$id_room_kelas
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
-- MY POIN DATA TEMPORER
-- ========================================
(
  SELECT rank_global FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_global,
(
  SELECT rank_kelas FROM tb_poin   
  WHERE id_room=$id_room  
  AND id_peserta=a.id) rank_kelas


FROM tb_peserta a 
JOIN tb_kelas_peserta b ON a.id=b.id_peserta 
JOIN tb_kelas c ON b.kelas=c.kelas 
JOIN tb_room_kelas d ON c.kelas=d.kelas 
WHERE a.status=1 -- peserta aktif only
AND password is not null -- peserta aktif pasti sudah ganti pass
AND a.id_role=1 -- peserta only tidak GM
AND $sql_id_peserta -- SWITCH VIEW PESERTA | GM
AND c.tahun_ajar = $tahun_ajar 
AND d.id_room = $id_room 
AND a.nama NOT LIKE '%dummy%' -- bukan peserta dummy 
AND a.nama LIKE '%ah%' -- DEBUGGING 


ORDER BY b.kelas, a.nama
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$jumlah_peserta = mysqli_num_rows($q);
if ($jumlah_peserta > 1 and $id_role == 1) die('Duplicate result found at nilai_akhir');









































# =======================================================
# ROOM KELAS UNTUK CSV
# =======================================================
$id_role = 1; //zzz debug
if ($id_role == 2) {
  $arr_kelas = [];
  $s = "SELECT * FROM tb_room_kelas WHERE id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $arr_kelas[$d['kelas']] = $d['id'];
  }
  foreach ($arr_kelas as $k => $jp) $data_csv[$k] = '';
}

# =======================================================
# LOOPING MAIN SELECT
# =======================================================
$tr_empty = '<tr><td colspan=100%>&nbsp;</td></tr>';



$no = 0;
$i = 0;
$last_kelas = '';
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $no++;

  // extract row value
  $nama_peserta = strtoupper($d['nama_peserta']);
  // $jumlah_ontime = $d['jumlah_ontime'];
  $count_sesi_aktif = $d['count_sesi_aktif'];
  if (!$count_sesi_aktif) die(div_alert('danger', "count_sesi_aktif : $count_sesi_aktif canot be null"));

  // handler blok tiap kelas
  $kelas_ini = $d['kelas'];
  if ($last_kelas != $kelas_ini and $i != 1) $tr .= $tr_empty;
  if ($last_kelas != $kelas_ini) {
    $tr .= $thead;
    $no = 1;

    // HEADER CSV
    if ($id_role == 2) {
      $reguler = $d['shift'] == 'P' ? 'Reguler' : 'NR';
      $data_csv[$kelas_ini] .= "\n\nDAFTAR HADIR MAHASISWA DAN NILAI UTS TAHUN AKADEMIK 2023-2024 GANJIL\n\n";
      $data_csv[$kelas_ini] .= "Prodi,$d[jenjang] - $d[nama_prodi] - $reguler\n";
      $data_csv[$kelas_ini] .= "Mata Kuliah,Matematika Informatika\n";
      $data_csv[$kelas_ini] .= "Semester / Kelas,$d[semester] / $d[kode_kelas]\n";
      $data_csv[$kelas_ini] .= "Dosen,Iin S.T. M.Kom\n\n";
      $data_csv[$kelas_ini] .= "NO,NAMA,NIM,TIMESTAMP KEHADIRAN,NILAI TUGAS,NILAI UTS,KETERANGAN\n";
    }
  }

  $nilai_harian = 'zzz';













  # ======================================================
  # rvalue_of HANDLER
  # ======================================================

  // inject from room_vars
  $d['total_count_presensi_offline'] = $count_sesi_aktif;
  $d['total_count_presensi_online'] = $count_sesi_aktif;
  $d['total_count_ontime'] = $count_sesi_aktif;
  $d['total_count_latihan'] = $total_latihan;
  $d['total_count_latihan_wajib'] = $total_latihan_wajib;
  $d['total_count_challenge'] = $total_challenge;
  $d['total_count_challenge_wajib'] = $total_challenge_wajib;
  $d['total_rank_global'] = $total_peserta;
  $d['total_rank_kelas'] = $total_peserta_kelas;
  $d['total_nilai_uts'] = 100;
  $d['total_nilai_uas'] = 100;
  $d['total_nilai_remed_uts'] = 100;
  $d['total_nilai_remed_uas'] = 100;

  foreach ($rbobot as $key => $value) {
    $my_value[$key] = $d[$key] ?? 0; // or zero
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

  if ($d['rank_global']) {
    $rkonversi['rank_global'] = round(110 - (($d['rank_global'] - 1) * ((round($total_peserta * 8 / 10, 0) / $total_peserta) * (100 / $total_peserta))), 0);
    if ($rkonversi['rank_global'] > 100) $rkonversi['rank_global'] = 100;
  } else {
    $rkonversi['rank_global'] = 0;
  }

  if ($d['rank_kelas']) {
    $rkonversi['rank_kelas'] = round(110 - (($d['rank_kelas'] - 1) * ((round($total_peserta_kelas * 8 / 10, 0) / $total_peserta_kelas) * (100 / $total_peserta_kelas))), 0);
    if ($rkonversi['rank_kelas'] > 100) $rkonversi['rank_kelas'] = 100;
  } else {
    $rkonversi['rank_kelas'] = 0;
  }

  $rkonversi['nilai_uts'] = $d['nilai_uts'];
  $rkonversi['nilai_remed_uts'] = $d['nilai_remed_uts'];
  $rkonversi['nilai_uas'] = $d['nilai_uas'];
  $rkonversi['nilai_remed_uas'] = $d['nilai_remed_uas'];
  # =======================================================
  # NILAI AKHIR BY LOOP CALCULATIONS
  # =======================================================
  $nilai_akhir = 0;
  foreach ($rbobot as $key => $value) $nilai_akhir += $rbobot[$key] * $rkonversi[$key];
  $nilai_akhir = round($nilai_akhir, 0);
  if ($nilai_akhir > 100) $nilai_akhir = 100;

  $td = '';
  $nilai_akhir = 0;
  foreach ($rbobot as $key => $value) {
    $kolom = str_replace('_', ' ', $key);
    if (strpos("salt$key", 'nilai_')) {
      $dikali_bobot = "$rbobot[$key]%";
    } else {
      $dikali_bobot = "$rkonversi[$key]x$rbobot[$key]%";
    }

    $sub_nilai_akhir = round(($rkonversi[$key] * $rbobot[$key]) / 100, 2);
    $nilai_akhir += $sub_nilai_akhir;
    $gradasi = $rbobot[$key] ?  gradasi($rkonversi[$key]) : '';

    $td .= "
      <td class='gradasi-$gradasi'>
        <div class=hideit>$kolom:</div>
        $rvalue_of[$key]
        <div class='miring abu f10'>$dikali_bobot</div>
        <div class='darkblue'>$sub_nilai_akhir</div>
      </td>
    ";
  }





















  # =======================================================
  # FINAL TR
  # =======================================================
  if ($id_role == 2) {
    $gradasi =   gradasi($nilai_akhir);

    $tr .= "
    <tr class='f14'>
      <td>$no</td>
      <td>
        $nama_peserta
        <div class='kecil miring abu'>$d[kelas]</div>
      </td>
      $td
      <td class='gradasi-$gradasi'>$nilai_akhir</td>
    </tr>";
  }

  //for repeat header
  $last_kelas = $kelas_ini;

  $tanggal_submit_uts = $d['tanggal_submit_uts'] ?? '-'; // ZZZ tanggal submit only for nilai_uts
  $nim = $d['nim'] ?? '-';
  if ($id_role == 2)  $data_csv[$kelas_ini] .= "$no,$nama_peserta,$nim,$tanggal_submit_uts,$nilai_harian,$d[nilai_uts],-\n";
}

$link_download_csv_uts = '';
$link_download_csv_uas = '';
$event_ujian = 'uts';
if ($id_role != 1) {
  foreach ($arr_kelas as $k => $jp) {
    if (strpos("salt$k", 'DEBUGER')) continue;
    if (strpos("salt$k", 'INSTRUKTUR')) continue;
    // echo "<pre class=debug>$data_csv[$k]</pre>";
    $path_csv = "csv/nilai-$event_ujian/$k.csv";
    $fcsv = fopen($path_csv, "w+") or die("$path_csv cannot accesible.");
    fwrite($fcsv, $data_csv[$k]);
    fclose($fcsv);
    $link_download_csv_uts .= "<a href='$path_csv' target=_blank class='btn btn-success btn-sm w-100 mb2'>$k</a> ";
    $link_download_csv_uas .= "<button class='btn btn-secondary btn-sm w-100 mb2'>$k</button> ";
  }
}


if ($id_role == 2) {
  $blok_kelas = "
    <table class='table'>
      $tr
    </table>
    <div class=wadah style='max-width:450px'>
      <div class=row>
        <div class=col-md-6>
          <div class=mb1>Download Nilai UTS:</div>
          $link_download_csv_uts
        </div>
        <div class=col-md-6>
          <div class=mb1>Download Nilai UAS:</div>
          $link_download_csv_uas
        </div>
      </div>
    </div>
  ";
} else {
  $single_show = '';
  $blok_kelas = '';
  $nilai_akhir = 0;
  foreach ($rbobot as $key => $value) {
    $sub_nilai_akhir = round(($rkonversi[$key] * $rbobot[$key]) / 100, 2);
    $nilai_akhir += $sub_nilai_akhir;
    $abu = $rbobot[$key] ? '' : 'abu f10 miring';
    $kolom = str_replace('_', ' ', $key);
    $kolom = str_replace('uts', 'UTS', $kolom);
    $kolom = str_replace('uas', 'UAS', $kolom);

    $sub_nilai_akhir_sty = ($rbobot[$key] and !$sub_nilai_akhir) ? 'red' : 'darkblue';
    $gradasi = $rbobot[$key] ?  gradasi($rkonversi[$key]) : '';

    $single_show .= "
      <div class='p2 $abu gradasi-$gradasi'>
        <div class=row>
          <div class='col-md-4 miring darkblue proper'>
            $kolom
          </div>
          <div class='col-md-3'>
            $rvalue_of[$key]
          </div>
          <div class='col-md-3'>
            $rkonversi[$key] <span class='kecil miring abu'>x $rbobot[$key]%</span>
          </div>
          <div class='col-md-2 kanan $sub_nilai_akhir_sty'>
            $sub_nilai_akhir
          </div>
        </div>
      </div>
    ";
  }

  $blok_kelas .= "
    <div class=wadah>
      <h3 class='darkblue mt3 mb3'>$nama_peserta <span class='miring abu kecil'>$kelas</span></h3>
      $single_show
      <div class='btop p2 gradasi-toska'>
        <div class=row>
          <div class='col-md-10 darkblue'>
            Nilai Akhir
          </div>
          <div class='col-md-2 f30 blue kanan'>
            $nilai_akhir
          </div>
        </div>
      </div>
    </div>
  ";

  // auto-save for self
  $s = "UPDATE tb_poin SET nilai_akhir=$nilai_akhir WHERE id_peserta=$id_peserta AND id_room=$id_room";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
}









$jumlah_peserta_show = $id_role == 1 ? '' : "<div class=mb2>Jumlah Peserta: $jumlah_peserta peserta</div>";
echo "
  <div data-aos=fade>
    $jumlah_peserta_show
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