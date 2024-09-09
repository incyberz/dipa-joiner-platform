<style>
  .border_blue {
    border: solid 3px blue;
  }

  .border_red {
    border: solid 1px #f55;
  }

  .border_green {
    border: solid 1px #5f5;
  }
</style>
<?php
# =================================================================
login_only();
include 'include/date_managements.php';
include 'presensi_processor.php';


$sql_target_kelas = $target_kelas ? "a.kelas='$target_kelas'" : '1';
$s = "SELECT 1 
FROM tb_kelas_peserta a 
JOIN tb_peserta b ON a.id_peserta=b.id 
JOIN tb_kelas c ON a.kelas=c.kelas
JOIN tb_room_kelas d ON c.kelas=d.kelas
WHERE b.status = 1 -- peserta aktif
AND b.id_role = 1 -- peserta only
AND $sql_target_kelas 
AND d.id_room = $id_room 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_peserta_presensi = mysqli_num_rows($q); // sesuai target kelas
$info_target_kelas = $target_kelas ? "<div>Target Kelas: $target_kelas</div>" : '';
$rekap = $id_role == 1 ? '<p class=f14>Presenting your work! Not only a signature.</p>' : "Admin only: <a href='?presensi_rekap'>Rekap Presensi</a>$info_target_kelas";
echo "
  <div class='section-title' data-aos-zzz='fade-up'>
    <h2>Presensi</h2>
    $rekap
  </div>
";


# =========================================================
# INITIAL VARIABLE
# =========================================================
$basic_point = 3000;
$persen_up = 117;
$multiplier_reduction_per_presenter = 3.4; //pengurangan poin per presenters
$syarat_soal_count = 1; // 1 soal per minggu
$is_ontime_now = 0;
$is_telat_now = 0;
$sudah_presensi = 0;
$dikurangi_persen = 50; // jika telat dikurangi 50% + durasi hari telat
$ris_ontime = [];
// $rpresenters_kelas = [];
$sesi_aktif = 0;




# =========================================================
# BASIC POINT UP IF ONTIME
# =========================================================
$s = "SELECT 
(
  SELECT id   
  FROM tb_presensi_summary 
  WHERE id_peserta=$id_peserta
  AND id_room=$id_room) id_presensi_summary,
(
  SELECT id   
  FROM tb_sesi a 
  WHERE a.awal_presensi >= '$ahad_skg'  
  AND a.akhir_presensi < '$ahad_depan' 
  AND a.id_room=$id_room) id_sesi_aktif,
(
  SELECT COUNT(1)  
  FROM tb_presensi a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  WHERE a.id_peserta=$id_peserta 
  AND a.is_ontime = 1 
  AND b.id_room=$id_room) jumlah_ontime,
(
  SELECT SUM(poin)  
  FROM tb_presensi a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  WHERE a.id_peserta=$id_peserta 
  AND b.id_room=$id_room) jumlah_poin_presensi,
(
  SELECT COUNT(1)  
  FROM tb_presensi a 
  JOIN tb_sesi b ON a.id_sesi=b.id 
  WHERE a.id_peserta=$id_peserta 
  AND b.id_room=$id_room) jumlah_presensi
  ";

// echo "<pre>$s</pre>";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$id_presensi_summary = $d['id_presensi_summary'];
# =========================================================
# CREATE PRESENSI SUMMARY IF NOT EXITS
# =========================================================
if ($id_presensi_summary == '') {
  $s = "INSERT INTO tb_presensi_summary (id_peserta,id_room) VALUES ($id_peserta,$id_room)";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

$jumlah_ontime = $d['jumlah_ontime'];
$jumlah_presensi = $d['jumlah_presensi'];
$id_sesi_aktif = $d['id_sesi_aktif'];
$jumlah_poin_presensi = $d['jumlah_poin_presensi'] ?? 0;
$syarat_play_count = $jumlah_presensi + 1;

for ($i = 1; $i <= $jumlah_ontime; $i++) {
  $basic_point = round($basic_point * $persen_up / 100, 0);
  // echo "<h1>$i x upper points | $syarat_play_count | $basic_point</h1>";
}




# =========================================================
# MAIN SELECT
# =========================================================
$div = '';
$target_kelas_presensi = $target_kelas ? $target_kelas : $kelas;
$s = "SELECT a.*,
a.id as id_sesi,
(
  SELECT COUNT(1) FROM tb_soal_peserta p 
  WHERE p.id_pembuat=$id_peserta 
  AND p.id_sesi=a.id) jumlah_soal,
(
  SELECT COUNT(1) FROM tb_paket_war p 
  WHERE id_peserta=$id_peserta 
  AND is_completed=1 
  AND id_room=$id_room) play_count , 
(
  SELECT 1 FROM tb_presensi 
  WHERE id_peserta=$id_peserta 
  AND id_sesi=a.id) sudah_presensi, 
(
  SELECT COUNT(1) FROM tb_presensi p 
  JOIN tb_peserta q ON p.id_peserta=q.id 
  JOIN tb_kelas_peserta r ON q.id=r.id_peserta  
  WHERE p.id_sesi=a.id 
  AND r.kelas='$target_kelas_presensi') count_yg_hadir, 
(
  SELECT COUNT(1) FROM tb_presensi p 
  JOIN tb_sesi q ON p.id_sesi=q.id 
  WHERE p.id_peserta=$id_peserta 
  AND q.id_room=$id_room) jumlah_presensi,
(
  SELECT jadwal_kelas FROM tb_sesi_kelas p 
  WHERE p.kelas='$target_kelas_presensi' 
  AND p.id_sesi=a.id) jadwal_kelas,
(
  SELECT COUNT(1) FROM tb_assign_latihan p 
  WHERE p.id_room_kelas='$id_room_kelas' 
  AND p.is_wajib=1
  AND p.id_sesi=a.id) latihan_wajib_count,
(
  SELECT COUNT(1) FROM tb_bukti_latihan p 
  JOIN tb_assign_latihan q ON p.id_assign_latihan=q.id  
  WHERE q.id_room_kelas='$id_room_kelas' 
  AND q.is_wajib=1
  AND q.id_sesi=a.id
  AND p.id_peserta=$id_peserta) my_latihan_wajib_count 



FROM tb_sesi a 
WHERE a.id_room=$id_room 
AND jenis = 1 -- sesi normal
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$presenters_kelas_last_active_sesi = 0;
$is_ontime_now = 0;
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {

  // $d['play_count'] = 999; //debug var
  // $d['jumlah_soal'] = 999; //debug var
  $i++;

  $id_sesi = $d['id_sesi'];
  $sudah_presensi = $d['sudah_presensi'];

  $jadwal_kelas = $d['jadwal_kelas'];
  $awal_presensi = $d['awal_presensi'];
  $akhir_presensi = $d['akhir_presensi'];

  $tnow = strtotime('now');
  $tawal = strtotime($awal_presensi);
  $takhir = strtotime($akhir_presensi);

  # ===============================================================
  # MINGGU SEKARANG
  # ===============================================================
  $is_ontime_now = 0;
  $is_telat_now = 0;
  $sudah_dibuka = $tnow >= $tawal ? 1 : 0;
  $belum_ditutup = $tnow < $takhir ? 1 : 0;
  $sedang_berlangsung = $sudah_dibuka && $belum_ditutup ? 1 : 0;

  $sesi_aktif++;
  $ris_ontime[$id_sesi] = 0;
  if ($sudah_dibuka) { // sudah dibuka
    // $rpresenters_kelas[$id_sesi] = $d['count_yg_hadir'];
    $presenters_kelas_last_active_sesi = $d['count_yg_hadir'];

    if ($belum_ditutup) { // berlangsung
      $is_ontime_now = 1;
      $ris_ontime[$id_sesi] = 1;
    } else { // sudah dibuka, dan sudah ditutup (lampau, telat presensi)
      $is_telat_now = 1;
    }
  } else { // belum dibuka
    $sesi_aktif--;
  }


  if ($jadwal_kelas) {
    $jadwal_kelas_show = date('D, M d, H:i', strtotime($jadwal_kelas));
    $jadwal_kelas_show .= ' ~ <span class=abu>' . eta(-$tnow + strtotime($d['jadwal_kelas'])) . '</span>';
  } else {
    $jadwal_kelas_show = $unset;
  }

  if ($awal_presensi) {
    $awal_presensi_show = date('M d, Y, H:i', $tawal);
    $awal_presensi_show .= ' ~ <span class=abu>' . eta(-$tnow + $tawal) . '</span>';
  } else {
    $awal_presensi_show = $unset;
  }

  if ($akhir_presensi) {
    $akhir_presensi_show = date('M d, Y, H:i', $takhir);
    $akhir_presensi_show .= ' ~ <span class=abu>' . eta(-$tnow + $takhir) . '</span>';
  } else {
    $akhir_presensi_show = $unset;
  }


  $akhir_presensi_show = $is_telat_now ? "<span class=red>$akhir_presensi_show</span>" : $akhir_presensi_show;


  if ($sudah_presensi) {
    $s2 = "SELECT * FROM tb_presensi WHERE id_peserta=$id_peserta AND id_sesi=$id_sesi";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $d2 = mysqli_fetch_assoc($q2);

    $poin = number_format($d2['poin'], 0);
    $tgl = date('D, M d, H:i:s', strtotime($d2['tanggal']));

    $img_ontime = '<img src="assets/img/icon/ontime.png" height=50px />';
    $img_late = '<img src="assets/img/icon/late.png" height=50px />';
    $img = $d2['is_ontime'] ? $img_ontime : $img_late;
    $sudah_presensi = $d2['is_ontime'] ? '<span class="biru tebal">Ontime</span>' : '<span class="darkred">Telat Presensi</span>';

    $btn_presensi = "
      <div >
        <div>$img</div>
        <div>$sudah_presensi <span class='miring abu'>at $tgl</span></div>
        <div>$poin LP</div>
      </div>
    ";

    $syarat_presensi = "
      <div class=mb2>Soal saya: $d[jumlah_soal] | <a href='?tanam_soal&id_sesi=$id_sesi'>Tanam Lagi</a></div> 
    ";
  } else { //belum presensi

    $jumlah_soal = $d['jumlah_soal'];
    $play_count = $d['play_count'];
    $syarat_soal = "$jumlah_soal of $syarat_soal_count";
    $syarat_play = "$play_count of $syarat_play_count";

    $my_latihan_wajib_count = $d['my_latihan_wajib_count'];
    $latihan_wajib_count = $d['latihan_wajib_count'];
    $syarat_latihan_wajib = "$my_latihan_wajib_count of $latihan_wajib_count";


    if ($jumlah_soal < $syarat_soal_count) {
      $syarat_soal = "<span class=red>$syarat_soal</span> | <a href='?tanam_soal&id_sesi=$id_sesi'>Tanam</a>";
    } else {
      $syarat_soal = "<span class=green>$syarat_soal</span> <img src='assets/img/icon/check.png' height=20px />";
    }

    if ($play_count < $syarat_play_count) {
      $syarat_play = "<span class=red>$syarat_play</span> | <a href='?perang_soal&mode=random'>Play</a>";
    } else {
      $syarat_play = "<span class=green>$syarat_play</span> <img src='assets/img/icon/check.png' height=20px />";
    }

    if ($latihan_wajib_count) {
      if ($my_latihan_wajib_count < $latihan_wajib_count) {
        $syarat_latihan_wajib = "<span class=red>$syarat_latihan_wajib</span> | <a href='?activity&jenis=latihan'>Kerjakan</a>";
      } else {
        $syarat_latihan_wajib = "<span class=green>$syarat_latihan_wajib</span> <img src='assets/img/icon/check.png' height=20px />";
      }
      $syarat_latihan_wajib = "<div class=mb2>Latihan wajib: $syarat_latihan_wajib</div>";
    } else {
      $syarat_latihan_wajib = '';
    }

    $dikurangi = '';
    if ($awal_presensi and $akhir_presensi) { // batasan presensi OK
      if ($jumlah_soal >= $syarat_soal_count and $play_count >= $syarat_play_count) { // boleh present
        if ($is_telat_now) {
          $saya_hadir = 'Saya Hadir Telat';
          $durasi_hari = $akhir_presensi ? durasi_hari($now, $akhir_presensi) : 0;
          $dikurangi_persen -= $durasi_hari;
          if ($dikurangi_persen > 90) $dikurangi_persen = 90;
          $lp = round($dikurangi_persen * $basic_point / 100, 0);
          $dikurangi = "<span class=red>Poin dikurangi $lp LP | <span id=dikurangi_persen>$dikurangi_persen</span>%</span>";
          $primary = 'warning';
        } else { // boleh present dan di sesi ini tidak telat
          if ($sudah_dibuka) {
            $saya_hadir = 'Set Saya Hadir';
            $dikurangi = '';
            $primary = 'primary';
          } else { // boleh present, tidak telat, tapi belum dibuka
            $saya_hadir = 'Belum opening... mohon tunggu!';
            $dikurangi = '';
            $primary = 'secondary';
          }
        }


        $btn_presensi = $sudah_dibuka ? "
          <form method=post>
            <button class='btn btn-$primary btn-sm btn-block' value=$id_sesi name=btn_saya_hadir><span class=f12>$saya_hadir</span></button>
            $dikurangi
          </form>
        " :
          "<button class='btn btn-$primary btn-sm btn-block' disabled><span class=f12>$saya_hadir</span></button>";
      } else { // syarat kurang
        $btn_presensi = "
          <button class='btn btn-secondary btn-sm btn-block' onclick='alert(\"Maaf, kamu belum memenuhi syarat presensi, periksalah yang bertanda merah.\")'><span class=f12>Set Saya Hadir</span></button>
        ";
      }
    } else {
      $btn_presensi = '<span class=red>Batasan Presensi Unset.</span> <div class="kecil darkblue mt1">Segera lapor ke instruktur!</div>';
    }


    $syarat_presensi = "
      <div class='abu miring'>Syarat presensi:</div> 
      <div class=mb1>Soal saya: $syarat_soal</div> 
      <div class=mb1>Play count: $syarat_play</div>
      $syarat_latihan_wajib
    ";
  }

  $border_blue = $is_ontime_now ? 'border_blue' : '';
  $border_blue = $is_telat_now ? 'border_red' : $border_blue;
  $border_blue = $sudah_presensi ? 'border_green' : $border_blue;
  $hijau = $is_ontime_now ? 'hijau' : '';


  if ($id_role == 2) {
    $id_sesi_kelas = $id_sesi . "__$target_kelas_presensi";
    $img_edit = img_icon('edit');

    $form_jadwal_kelas_toggle = "<span class='btn_aksi' id=form_jadwal_kelas$id_sesi" . "__toggle>$img_edit</span>";
    $tanggal_sesi = $jadwal_kelas ? date('Y-m-d', strtotime($jadwal_kelas)) : '';
    $jam_sesi = $jadwal_kelas ? date('H:i', strtotime($jadwal_kelas)) : '';
    $form_jadwal_kelas = "
      <div class='hideit wadah gradasi-kuning' id=form_jadwal_kelas$id_sesi>
        <form method=post>
          Tanggal sesi
          <input type=date required class='form-control form-control-sm mb2' name=tanggal_sesi value='$tanggal_sesi'>
          Jam sesi
          <input type=time required class='form-control form-control-sm mb2' name=jam_sesi value='$jam_sesi'>
          <div class=mb2>
            <label>
              <input type=checkbox checked name=update_next_week> 
              Update pula untuk sesi minggu berikutnya (sesuai dg jadwal sesi ini)
            </label>
          </div>
          <button class='btn btn-primary btn-sm' name=btn_update_jadwal_kelas value=$id_sesi_kelas>Update Jadwal Tatap Muka :: $target_kelas_presensi</button>
        </form>
      </div>
    ";

    $form_durasi_presensi_toggle = "<span class='btn_aksi' id=form_durasi_presensi$id_sesi" . "__toggle>$img_edit</span>";
    $form_durasi_presensi = "
      <div class='hideit wadah gradasi-kuning' id=form_durasi_presensi$id_sesi>
        <form method=post>
          <div class='mb2 darkblue'>)* Durasi Presensi berlaku untuk seluruh kelas pada room ini</div>
          Pembukaan
          <input required class='form-control form-control-sm mb2' name=awal_presensi value='$awal_presensi' placeholder='Format YYYY-MM-DD HH:MM'>
          Penutupan
          <input required class='form-control form-control-sm mb2' name=akhir_presensi value='$akhir_presensi' placeholder='Format YYYY-MM-DD HH:MM'>
          <div class=mb2>
            <label>
              <input type=checkbox checked name=update_next_week> 
              Update pula untuk sesi minggu berikutnya (sesuai dg jadwal sesi ini)
            </label>
          </div>
          <button class='btn btn-primary btn-sm' name=btn_update_durasi_presensi value=$id_sesi>Update Durasi Presensi :: id-$id_sesi</button>
        </form>
      </div>
    ";
  } else {
    $form_jadwal_kelas = '';
    $form_durasi_presensi = '';
    $form_jadwal_kelas_toggle = '';
    $form_durasi_presensi_toggle = '';
  }

  $j = 0;
  $div .= "
    <div class='gradasi-$hijau mb2 bordered br5 p2 f12 $border_blue' id='row_presensi__$id_sesi'>
      <div class='row'>
        <div class='col-lg-3'>
          <div class='mb1 darkblue tebal'>P$i $d[nama]</div>
          <div class='mb1 abu miring'>$d[count_yg_hadir] of $total_peserta_presensi sudah hadir</div>
        </div>
        <div class='col-lg-4'>
          <div><span class='abu miring'>Jadwal Tatap Muka:</span> $jadwal_kelas_show $form_jadwal_kelas_toggle</div>
          $form_jadwal_kelas

          <div><span class='abu miring'>Opening:</span> $awal_presensi_show</div>
          <div class=mb1><span class='abu miring'>Closing:</span> $akhir_presensi_show $form_durasi_presensi_toggle</div>
          $form_durasi_presensi
          
        </div>
        <div class='col-lg-2'>
          $syarat_presensi
        </div>
        <div class='col-lg-3'>
          $btn_presensi
        </div>
      </div>
    </div>
  ";
}



$basic_point -= round($presenters_kelas_last_active_sesi * $multiplier_reduction_per_presenter, 0);
$epp_detik = $basic_point;
$epp_milidetik = 99 - (date('s') % 80) . rand(22, 99);

# =========================================================
# POST HANDLER
# =========================================================
if (isset($_POST['btn_saya_hadir'])) {
  $id = $_POST['btn_saya_hadir'];

  $s = "SELECT 1 FROM tb_presensi WHERE id_sesi=$id AND id_peserta=$id_peserta";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) == 0) {
    $poin = $ris_ontime[$id] ? $basic_point : $basic_point - round($basic_point * $dikurangi_persen / 100, 0);
    $s = "INSERT INTO tb_presensi 
    (id_peserta,id_sesi,poin,is_ontime) VALUES 
    ($id_peserta,$id,$poin,$ris_ontime[$id])";
    // echo $s;
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }

  jsurl('?presensi');
}


# =========================================================
# UPDATE DATA PRESENSI
# =========================================================
$s = "UPDATE tb_presensi_summary SET 
jumlah_ontime = $jumlah_ontime,
jumlah_presensi = $jumlah_presensi,
poin_presensi = $jumlah_poin_presensi,
last_update = CURRENT_TIMESTAMP 
WHERE id_peserta=$id_peserta 
AND id_room=$id_room 
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));


# =========================================================
# FINAL OUTPUT
# =========================================================
$persen_ontime = $sesi_aktif == 0 ? 0 : round($jumlah_ontime / $sesi_aktif * 100, 0);
$epp_milidetik = $persen_ontime == 100 ? '0000' : $epp_milidetik;
echo "
<div class='tengah wadah' style='max-width:500px; margin:auto; margin-bottom: 30px'>
  <div>Your Present:</div>
  <div class='f50 darkblue'><span id=persen_ontime>$persen_ontime</span>%</div>
  <div class=' kecil abu miring'>$jumlah_ontime of $sesi_aktif active sessions | Poin: $jumlah_poin_presensi LP</div>
  <hr>
  <div class='kecil abu '>Ontime Points next: <span class='consolas darkred'><span id=epp_detik>$epp_detik</span>.<span id=epp_milidetik>$epp_milidetik</span> LP</span></div>
</div>
$div";

if ($persen_ontime != 100) {
  echo "
    <script>
      $(function(){
        let epp_detik = $('#epp_detik').text();
        let epp_milidetik = $('#epp_milidetik').text();

        let = setInterval(() => {
          if(epp_milidetik==0){
            epp_milidetik=9999;
            epp_detik--;
          }else{
            epp_milidetik--;
          }
          $('#epp_detik').text(epp_detik);
          $('#epp_milidetik').text(epp_milidetik);
        }, 35);

      })
    </script>
  ";
}













if ($id_role == 2) include 'admin/manage_presensi.php';
?>